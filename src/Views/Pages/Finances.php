<?php
    namespace Framework\Views\Pages;

    /**
     * Lewis Lancaster 2016
     *
     * Class Finances
     *
     * @package Framework\Views\Pages
     */

    use Framework\Application\Render;
    use Framework\Application\Container;
    use Framework\Application\Session;
    use Framework\Application\Utilities\PostHelper;
    use Framework\Syscrack\Game\Finance;
    use Framework\Views\BaseClasses\Page as BaseClass;
    use Framework\Views\Structures\Page as Structure;

    class Finances extends BaseClass implements Structure
    {

        /**
         * @var Finance
         */

        protected $finance;


        /**
         * Finances constructor.
         */

        public function __construct()
        {

            if( isset( $this->finance ) == false )
                $this->finance = new Finance();

            parent::__construct( true, true, true, true );
        }

        /**
         * Returns the pages flight mapping
         *
         * @return array
         */

        public function mapping()
        {

            return array(
                [
                    '/finances/', 'page'
                ],
                [
                    'GET /finances/transfer/', 'transfer'
                ],
                [
                    'POST /finances/transfer', 'transferProcess'
                ]
            );
        }

        /**
         * Default page
         */

        public function page()
        {

            Render::view('syscrack/page.finances', [], $this->model());
        }

        public function transfer()
        {

            Render::view('syscrack/page.finances.transfer', [], $this->model());
        }

        public function transferProcess()
        {

            if( PostHelper::hasPostData() == false )
            {

                $this->page();
            }
            else
            {

                if( PostHelper::checkForRequirements(['accountnumber','targetaccount','ipaddress','amount'] ) == false )
                {

                    $this->redirectError('Missing information', 'finances/transfer' );
                }

                $accountnumber = PostHelper::getPostData('accountnumber');
                $targetaccount = PostHelper::getPostData('targetaccount');
                $ipaddress = PostHelper::getPostData('ipaddress');
                $amount = PostHelper::getPostData('amount');

                if( is_numeric( $amount ) == false )
                {

                    $this->redirectError('Please enter a number for the amount', 'finances/transfer' );
                }

                $amount = abs( $amount );

                if( $amount == 0 )
                {

                    $this->redirectError('Please enter a number higher than zero', 'finances/transfer' );
                }

                if( $accountnumber == $targetaccount )
                {

                    $this->redirectError('You cant transfer to your self, funnily enough', 'finances/transfer');
                }

                if( $this->finance->accountNumberExists( $accountnumber ) == false )
                {

                    $this->redirectError('Account does not exist', 'finances/transfer');
                }

                if( $this->finance->accountNumberExists( $targetaccount ) == false )
                {

                    $this->redirectError('Account does not exist', 'finances/transfer');
                }

                $account = $this->finance->getByAccountNumber( $accountnumber );

                if( $account->userid !== $this->session->getSessionUser() )
                {

                    $this->redirectError('You do not own this account', 'finances/transfer');
                }

                $target = $this->finance->getByAccountNumber( $targetaccount );

                if( $this->computer->getComputer( $target->computerid )->ipaddress !== $ipaddress )
                {

                    $this->redirectError('Account does not exist at remote bank', 'finances/transfer');
                }

                if( $this->finance->canAfford( $account->computerid, $this->session->getSessionUser(), $amount ) == false )
                {

                    $this->redirectError('You cannot afford this transaction', 'finances/transfer' );
                }

                $this->finance->deposit( $target->computerid, $target->userid, $amount );

                $this->finance->withdraw( $account->computerid, $account->userid, $amount );

                $this->redirectSuccess('finances/transfer');
            }
        }
    }