<?php

    namespace ccwp;

    use ccwp\setup\setup;
    use ccwp\setup\enqueue;
    use ccwp\setup\menus;
    use ccwp\setup\remove;

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
            new Setup();
            new Enqueue();
            new Menus();
            new Remove();
        }
    }

?>
