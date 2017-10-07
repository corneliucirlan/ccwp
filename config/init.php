<?php

    /**
     * Init class
     *
     * @package ccwp
     */

    namespace ccwp;

    use ccwp\core\tags;
    use ccwp\core\widgets;
    use ccwp\core\cpt;
    use ccwp\api\settings;
    use ccwp\api\customizer;
    use ccwp\setup\setup;
    use ccwp\setup\enqueue;
    use ccwp\setup\menus;
    use ccwp\setup\remove;
    use ccwp\setup\login;

    class Init
    {
        private static $loaded = false;

        /**
         * Construct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct()
        {
            $this->initClasses();
        }

        /**
         * Initialise classes
         */
        public function initClasses()
        {
            // Check if class was already loaded
            if (self::$loaded) return;

            // Set loaded flag
            self::$loaded = true;

            // Call classes
            new Tags();
            new Widgets();
            new CPT();
            new Settings();
            new Customizer();
            new Setup();
            new Enqueue();
            new Menus();
            new Remove();
            new Login();
        }
    }

?>
