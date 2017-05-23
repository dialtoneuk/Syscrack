<?php
    namespace Framework\Application;

    /**
     * Lewis Lancaster 2017
     *
     * Class Mailer
     *
     * @package Framework\Application
     *
     * LET THE FUN BEGIN
     */

    use Exception;
    use Framework\Application\Utilities\FileSystem;
    use Framework\Exceptions\ApplicationException;
    use PHPMailer;

    class Mailer
    {

        /**
         * @var PHPMailer
         */

        protected $mailer;

        /**
         * Mailer constructor.
         *
         * @param bool $autoload
         */

        public function __construct( $autoload=true )
        {

            $this->mailer = new PHPMailer();

            $this->mailer->SMTPDebug = 2;

            if( $autoload == true )
            {

                $this->initializeMailer();
            }
        }

        /**
         * Sends an email
         *
         * @param $body
         *
         * @param string $subject
         *
         * @param $recipient
         *
         * @return bool
         */

        public function send($body, $subject = 'Verify your email', $recipient )
        {

            $this->mailer->setFrom( Settings::getSetting('mailer_from_address' ) );

            if( filter_var( $recipient, FILTER_VALIDATE_EMAIL ) == false )
            {

                throw new ApplicationException();
            }

            $this->mailer->addAddress( $recipient );

            $this->mailer->Subject = $subject;

            $this->mailer->Body = $body;

            if( Settings::getSetting('mailer_html') == true )
            {

                $this->mailer->isHTML( true );
            }

            try
            {

                return $this->mailer->send();
            }
            catch( Exception $error )
            {

                return false;
            }
        }

        /**
         * Parses an email
         *
         * @param $body
         *
         * @param array $variables
         *
         * @return mixed
         */

        public function parse( $body, array $variables )
        {

            if( is_string( $body ) == false )
            {

                throw new ApplicationException();
            }

            foreach( $variables as $key=>$variable )
            {

                $body = str_replace("%{$key}%", $variable, $body);
            }

            return $body;
        }

        /**
         * Inits the Mailer
         */

        public function initializeMailer()
        {

            $settings = Settings::getSetting('mailer_settings');

            foreach( $settings as $key=>$value )
            {

                $key = ucfirst( $key );

                if( property_exists ( $this->mailer, $key ) == false )
                {

                    throw new ApplicationException( $key . ' does not exist in mailer');
                }

                $this->mailer->{ $key } = $value;
            }

            if( Settings::getSetting('mailer_use_stmp') == true )
            {

                $this->mailer->isSMTP();
            }
        }

        /**
         * Gets the error info
         *
         * @return string
         */

        public function getErrorInfo()
        {

            return $this->mailer->ErrorInfo;
        }

        /**
         * Reads a template file
         *
         * @param $file
         *
         * @return string
         */

        public function getTemplate( $file )
        {

            if( FileSystem::hasFileExtension( $file ) == false )
            {

                $file = $file . '.php';
            }

            return FileSystem::read( $this->getFilePath() . $file );
        }

        /**
         * Gets the file path for the mail templates
         *
         * @return mixed
         */

        private function getFilePath()
        {

            return Settings::getSetting('mailer_template_location');
        }
    }