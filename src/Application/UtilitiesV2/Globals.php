<?php
	/**
	 * Created by PhpStorm.
	 * User: newsy
	 * Date: 03/06/2019
	 * Time: 11:26
	 */

	namespace Framework\Application\UtilitiesV2;

	/**
	 * Class Globals
	 * @package Framework\Application\UtilitiesV2
	 *
	 *
     * @property string string SYSCRACK_URL_ROOT
     * @property string SYSCRACK_NAMESPACE_ROOT Framework\\
     * @property string SYSCRACK_URL_ADDRESS http://localhost
     * @property string SYSCRACK_VERSION_PHASE alpha
     * @property float SYSCRACK_VERSION_NUMBER 0.1.5
     * @property string FRAMEWORK_BASECLASS base
     * @property string WEBSITE_TITLE Syscrack
     * @property string WEBSITE_JQUERY jquery-3.3.1.min.js
     * @property string WEBSITE_BOOTSTRAP4 bootstrap.js
     * @property string ACCOUNT_PREFIX user
     * @property int ACCOUNT_DIGITS 8
     * @property int ACCOUNT_RND_MIN 1
     * @property int ACCOUNT_RND_MAX 8
     * @property int ACCOUNT_PASSWORD_MIN 8
     * @property string ACCOUNT_PASSWORD_STRICT false
     * @property string TRACK_PRIVACY_PUBLIC public
     * @property string TRACK_PRIVACY_PRIVATE private
     * @property string TRACK_PRIVACY_PERSONAL personal
     * @property string TRACK_PREFIX track
     * @property int TRACK_NAME_MAXLENGTH 64
     * @property int TRACK_DIGITS 12
     * @property int TRACK_RND_MIN 0
     * @property int TRACK_RND_MAX 9
     * @property string UPLOADS_TEMPORARY_DIRECTORY files/temp/
     * @property string UPLOADS_FILEPATH src/Framework/Uploads/
     * @property string UPLOADS_NAMESPACE Framework\\Application\\Uploads\\
     * @property string UPLOADS_LOCAL true  //Will keep files in the temporary directory instead of uploading them ( should only be used for testing )
     * @property string UPLOADS_WAVEFORMS_LOCAL true  //Will keep wave forms in the temporary directory instead of uploading them. Use with global above to completely turn off the data handler.
     * @property string UPLOADS_POST_KEY track
     * @property int UPLOADS_MAX_SIZE_GLOBAL 500 //Used to define the max applicable size anybody can upload
     * @property int UPLOADS_ERROR_NOT_FOUND 1
     * @property int UPLOADS_ERROR_FILENAME 2
     * @property int UPLOADS_ERROR_EXTENSION 3
     * @property int UPLOADS_ERROR_TOO_LARGE 4
     * @property int UPLOADS_ERROR_CANCELLED 5
     * @property string SCRIPTS_ROOT src/Application/UtilitiesV2/Scripts/
     * @property string SCRIPTS_NAMESPACE Framework\\Application\\UtilitiesV2\\Scripts\\
     * @property string SCRIPTS_REQUIRE_CMD true
     * @property string FFMPEG_CONFIG_FILE data/config/ffmpeg.json
     * @property string VERIFICATIONS_NAMESPACE Framework\\Application\\Verifications\\
     * @property string VERIFICATIONS_ROOT src/Framework/Verifications/
     * @property string VERIFICATIONS_TYPE_EMAIL email
     * @property string VERIFICATIONS_TYPE_MOBILE mobile
     * @property string AMAZON_BUCKET_URL https://s3.eu-west-2.amazonaws.com/colourspace/
     * @property string AMAZON_CREDENTIALS_FILE data/config/storage/amazon.json
     * @property string AMAZON_S3_BUCKET colourspace
     * @property string AMAZON_LOCATION_US_WEST us-west-1
     * @property string AMAZON_LOCATION_US_WEST_2 us-west-2
     * @property string AMAZON_LOCATION_US_EAST us-east-1
     * @property string AMAZON_LOCATION_US_EAST_2 us-east-2
     * @property string AMAZON_LOCATION_CA_CENTRAL ca-central-1
     * @property string AMAZON_LOCATION_EU_WEST eu-west-1
     * @property string AMAZON_LOCATION_EU_WEST_2 eu-west-2
     * @property string AMAZON_LOCATION_EU_CENTRAL eu-central-1
     * @property string GOOGLE_RECAPTCHA_ENABLED false
     * @property string GOOGLE_RECAPTCHA_CREDENTIALS data/config/google_recaptcha.json
     * @property string GOOGLE_CLOUD_CREDENTIALS  data/config/storage/google.json
     * @property string STORAGE_CONFIG_ROOT cdata/config/storage/
     * @property string STORAGE_SETTINGS_FILE settings.json
     * @property string FLIGHT_JQUERY_FILE jquery-3.3.1.min.js
     * @property string FLIGHT_CONTENT_OBJECT true  //Instead convert $model into an object ( which is cleaner )
     * @property string FLIGHT_MODEL_DEFINITION model
     * @property string FLIGHT_PAGE_DEFINITION page
     * @property string FLIGHT_SET_GLOBALS true
     * @property string FLIGHT_VIEW_FOLDER themes
     * @property string TWIG_VIEW_FOLDER themes
     * @property string SETUP_ROOT src/Application/UtilitiesV2/Setups/
     * @property string SETUP_NAMESPACE Framework\\Application\\UtilitiesV2\\Setups\\
     * @property string MVC_NAMESPACE Framework\\Application\\MVC\\
     * @property string MVC_NAMESPACE_MODELS Models
     * @property string MVC_NAMESPACE_VIEWS Views
     * @property string MVC_NAMESPACE_CONTROLLERS Controllers
     * @property string MVC_TYPE_MODEL model
     * @property string MVC_TYPE_VIEW view
     * @property string MVC_TYPE_CONTROLLER controller
     * @property string MVC_REQUEST_POST POST
     * @property string MVC_REQUEST_GET GET
     * @property string MVC_REQUEST_PUT PUT
     * @property string MVC_REQUEST_DELETE DELETE
     * @property string MVC_ROUTE_FILE config/routes.json
     * @property string MVC_ROOT src/Views/MVC/
     * @property string MAKER_FILEPATH src/Application/UtilitiesV2/Makers/
     * @property string MAKER_NAMESPACE Framework\\Application\\UtilitiesV2\\Makers\\
     * @property int PAGE_SIZE 6  //Default page size: 6 objects wide
     * @property string FORM_ERROR_GENERAL general_error
     * @property string FORM_ERROR_INCORRECT incorrect_information
     * @property string FORM_ERROR_MISSING missing_information
     * @property string FORM_MESSAGE_SUCCESS success_message
     * @property string FORM_MESSAGE_INFO info_message
     * @property string FORM_DATA data
     * @property string RESOURCE_COMBINER_ROOT data/config/
     * @property string RESOURCE_COMBINER_CHMOD true
     * @property int RESOURCE_COMBINER_CHMOD_PERM 0755
     * @property string RESOURCE_COMBINER_PRETTY true
     * @property string RESOURCE_COMBINER_FILEPATH data/resources.bundle
     * @property string FIELD_TYPE_INCREMENTS increments
     * @property string FIELD_TYPE_STRING string
     * @property string FIELD_TYPE_INT integer
     * @property string FIELD_TYPE_PRIMARY primary
     * @property string FIELD_TYPE_TIMESTAMP timestamp
     * @property string FIELD_TYPE_DECIMAL decimal
     * @property string FIELD_TYPE_JSON json
     * @property string FIELD_TYPE_IPADDRESS ipAddress
     * @property string COLUMN_USERID userid
     * @property string COLUMN_SESSIONID sessionid
     * @property string COLUMN_CREATION creation
     * @property string COLUMN_METAINFO metainfo
     * @property string COLUMN_TRACKID trackid
     * @property string TABLES_NAMESPACE Framework\\Database\\Tables\\
     * @property string TABLES_ROOT src/Database/Tables/
     * @property string TESTS_NAMESPACE Framework\\Application\\UtilitiesV2\\Tests\\
     * @property string TESTS_ROOT src/Application/UtilitiesV2/Tests/
     * @property string AUDIT_TYPE_BAN ban
     * @property string AUDIT_TYPE_WARNING warning
     * @property string AUDIT_TYPE_GROUPCHANGE groupchange
     * @property string LOG_ROOT data/config/
     * @property string LOG_TYPE_GENERAL general
     * @property string LOG_TYPE_WARNING warning
     * @property string LOG_TYPE_DEFAULT default
     * @property string AUTOEXEC_ROOT src/Application/UtilitiesV2/AutoExecs/
     * @property string AUTOEXEC_NAMESPACE Framework\\Application\\UtilitiesV2\\AutoExecs\\
     * @property string AUTOEXEC_SCRIPTS_ROOT resources/scripts/
     * @property int AUTOEXEC_LOG_REFRESH 12  //In hours
     * @property string AUTOEXEC_LOG_LOCATION data/config/log/
     * @property string DATABASE_ENCRYPTION false
     * @property string DATABSAE_ENCRYPTION_KEY null  //Replace null with a string of a key to not use a rand gen key.
     * @property string DATABASE_CREDENTIALS data/config/database/connection.json
     * @property string DATABASE_MAP data/config/database/databaseschema.json
     * @property string GROUPS_ROOT data/config/groups/
     * @property string GROUPS_DEFAULT default
     * @property string GROUPS_FLAG_MAXLENGTH uploadmaxlength
     * @property string GROUPS_FLAG_MAXSIZE uploadmaxsize
     * @property string GROUPS_FLAG_LOSSLESS lossless
     * @property string GROUPS_FLAG_ADMIN admin
     * @property string GROUPS_FLAG_DEVELOPER developer
     * @property string USER_PERMISSIONS_ROOT data/config/user/
     * @property string FEATURED_ROOT data/featured/
     * @property string FEATURED_ARTISTS artists
     * @property string FEATURED_TRACKS tracks
     * @property string STREAMS_MP3 mp3
     * @property string STREAMS_FLAC flac
     * @property string STREAMS_OGG ogg
     * @property string STREAMS_WAV wav
     * @property string DEBUG_ENABLED true  //Will write debug messages and echo them inside the terminal instance
     * @property string DEBUG_WRITE_FILE true
     * @property string DEBUG_MESSAGES_FILE data/cli/messages.json
     * @property string DEBUG_TIMERS_FILE data/cli/timers.json
     * @property string MAILER_CONFIGURATION_FILE data/config/templates.json
     * @property string MAILER_TEMPLATES_ROOT resources/email/
     * @property string MAILER_IS_HTML true
     * @property string MAILER_IS_SMTP true
     * @property string MAILER_FROM_ADDRESS user00000001@Syscrack.io
     * @property string MAILER_FROM_USER user00000001
     * @property string MAILER_CONTACT_ADDRESS support@Syscrack.io
     * @property string MAILER_VERIFY_TEMPLATE email
     * @property string MAILER_BANNED_TEMPLATE banned
     * @property string MAILER_REMOVED_TEMPLATE removed
     * @property string MAILER_POSTED_TEMPLATE posted
     * @property string MAILER_COMMENTS_TEMPLATE comments
     * @property string SCRIPT_BUILDER_ENABLED true  //It isnt recommended you turn this on unless your compiled.js for some reason is missing or you are developing.
     * @property string SCRIPT_BUILDER_ROOT resources/scripts/
     * @property int SCRIPT_BUILDER_FREQUENCY 60 * 60 * 2 //Change the last digit for hours. Remove a * 60 for minutes.
     * @property string SCRIPT_BUILDER_COMPILED compiled.js
     * @property string SCRIPT_BUILDER_FORCED false //Compiles a fresh build each request regardless of frequency setting.
     * @property string COLLECTOR_DEFAULT_NAMESPACE Framework\\Application\\
     * @property int COLOURS_OUTPUT_HEX 1
     * @property int COLOURS_OUTPUT_RGB 2
     * @property string SHOP_ROOT src/Framework/Items/
     * @property string SHOP_NAMESPACE Framework\\Application\\Items\\
     * @property string SHOP_INVENTORY data/config/shop/items.json
     * @property string BALANCE_DEFAULT_AMOUNT 100
     * @property string TRANSACTION_TYPE_WITHDRAW withdraw
     * @property string TRANSACTION_TYPE_DEPOSIT deposit
     * @property string MIGRATOR_ROOT src/Application/UtilitiesV2/Migrators/
     * @property string MIGRATOR_NAMESPACE Framework\\Application\\UtilitiesV2\\Migrators\\
     * @property string PHPUNIT_FINISHED true
     * @property string CLI_DEFAULT_COMMAND instance
	 */

	class Globals
	{

		protected $array = [];

		/**
		 * Globals constructor.
		 *
		 * @param $globals
		 */

		public function __construct( $globals ) {

			foreach( $globals as $global)
				$this->array[ $global[0] ] = $global[1];
		}

		/**
		 * @param $name
		 *
		 * @return mixed
		 */

		public function __get($name)
		{

			if( isset( $this->array[ $name ] ) == false )
				throw new \Error("Global " . $name . " does not exist");

			return($this->array[ $name ] );
		}
	}