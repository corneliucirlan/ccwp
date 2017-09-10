<?php

    /**
     * Custom Post Type class
     *
     * @package ccwp
     */

    namespace ccwp\core;

    class CPT
    {
        /**
	     * Post type name.
	     *
	     * @var string $postTypeName Holds the name of the post type.
	     */
	    public $postTypeName;

	    /**
	     * Holds the singular name of the post type. This is a human friendly
	     * name, capitalized with spaces assigned on __construct().
	     *
	     * @var string $singular Post type singular name.
	     */
	    public $singular;

	    /**
	     * Holds the plural name of the post type. This is a human friendly
	     * name, capitalized with spaces assigned on __construct().
	     *
	     * @var string $plural Singular post type name.
	     */
	    public $plural;

	    /**
	     * Post type slug. This is a robot friendly name, all lowercase and uses
	     * hyphens assigned on __construct().
	     *
	     * @var string $slug Holds the post type slug name.
	     */
	    public $slug;

	    /**
	     * User submitted options assigned on __construct().
	     *
	     * @var array $options Holds the user submitted post type options.
	     */
	    public $options;

	    /**
	     * Taxonomies
	     *
	     * @var array $taxonomies Holds an array of taxonomies associated with the post type.
	     */
	    public $taxonomies;

	    /**
	     * Taxonomy settings, an array of the taxonomies associated with the post
	     * type and their options used when registering the taxonomies.
	     *
	     * @var array $taxonomySettings Holds the taxonomy settings.
	     */

	    public $taxonomySettings;
	    /**
	     * Exisiting taxonomies to be registered after the posty has been registered
	     *
	     * @var array $existingTaxonomies holds exisiting taxonomies
	     */
	    public $existingTaxonomies;

	    /**
	     * Taxonomy filters. Defines which filters are to appear on admin edit
	     * screen used in add_taxonmy_filters().
	     *
	     * @var array $filters Taxonomy filters.
	     */
	    public $filters;

	    /**
	     * Defines which columns are to appear on the admin edit screen used
	     * in addAdminColumns().
	     *
	     * @var array $columns Columns visible in admin edit screen.
	     */
	    public $columns;

	    /**
	     * User defined public functions to populate admin columns.
	     *
	     * @var array $customPopulateColumns User public functions to populate columns.
	     */
	    public $customPopulateColumns;

	    /**
	     * Sortable columns.
	     *
	     * @var array $sortable Define which columns are sortable on the admin edit screen.
	     */
	    public $sortable;

	    /**
	     * Textdomain used for translation. Use the setTextdomain() method to set a custom textdomain.
	     *
	     * @var string $textdomain Used for internationalising. Defaults to "cpt" without quotes.
	     */
	    public $textdomain = 'ccwp';

	    /**
	     * Constructor
	     *
	     * Register a custom post type.
	     *
	     * @param mixed $postTypeNames The name(s) of the post type, accepts (post type name, slug, plural, singular).
	     * @param array $options User submitted options.
	     */
	    public function __construct($postTypeNames = 'ccwp', $options = array())
	    {
			// Check if post type names is a string or an array.
	        if (is_array($postTypeNames)):

		            // Add names to object.
		            $names = array(
		                'singular',
		                'plural',
		                'slug'
		            );

		            // Set the post type name.
		            $this->postTypeName = $postTypeNames['post_type_name'];

		            // Cycle through possible names.
                    foreach ($names as $name):

		                // If the name has been set by user.
		                if (isset($postTypeNames[$name])):

								// Use the user setting
								$this->$name = $postTypeNames[$name];

			                // Else generate the name.
			                else:

			                    // define the method to be used
			                    $method = 'get_' . $name;

			                    // Generate the name
			                    $this->$name = $this->$method();
		                endif;
		            endforeach;

		        // Else the post type name is only supplied.
		        else:

		            // Apply to post type name.
		            $this->postTypeName = $postTypeNames;

		            // Set the slug name.
		            $this->slug = $this->getSlug();

		            // Set the plural name label.
		            $this->plural = $this->getPlural();

		            // Set the singular name label.
		            $this->singular = $this->getSingular();
			endif;

	        // Set the user submitted options to the object.
	        $this->options = $options;

	        // Register taxonomies.
	        add_action('init', array(&$this, 'registerTaxonomies'));

	        // Register the post type.
	        add_action('init', array(&$this, 'registerPostType'));

	        // add dashboard glance item
	        add_filter('dashboard_glance_items', array(&$this, 'dashboadGlance'));

			// add recent activity
			add_filter('dashboardRecentPosts_query_args', array(&$this, 'dashboardRecentPosts'), 10, 1);

	        // Register exisiting taxonomies.
	        add_action('init', array(&$this, 'registerExisitingTaxonomies'));

	        // Add taxonomy to admin edit columns.
	        add_filter('manage_edit-'.$this->postTypeName.'_columns', array(&$this, 'addAdminColumns'));

	        // Populate the taxonomy columns with the posts terms.
	        add_action('manage_'.$this->postTypeName.'_posts_custom_column', array(&$this, 'populateAdminColumns'), 10, 2);

	        // Add filter select option to admin edit.
	        add_action('restrict_manage_posts', array(&$this, 'addTaxonomyFilters'));

            // rewrite post update messages
	        add_filter('post_updated_messages', array(&$this, 'updatedMessages'));
	        add_filter('bulk_post_updated_messages', array(&$this, 'bulkUpdatedMessages'), 10, 2);
		}

	    /**
	     * Get variable
	     *
	     * Helper public function to get an object variable.
	     *
	     * @param string $var The variable you would like to retrieve.
	     * @return mixed Returns the value on success, boolean false whe it fails.
	     */
	    public function getVariable($var)
	    {
			// If the variable exists.
			if ($this->$var)
	            return $this->$var;

			return false;
	    }

	    /**
	     * Set variable
	     *
	     * Helper public function used to set an object variable. Can overwrite existsing
	     * variables or create new ones. Cannot overwrite reserved variables.
	     *
	     * @param mixed $var The variable you would like to create/overwrite.
	     * @param mixed $value The value you would like to set to the variable.
	     */
	    public function setVariable($var, $value)
	    {
			// An array of reserved variables that cannot be overwritten.
			$reserved = array(
				'config',
				'post_type_name',
				'singular',
				'plural',
				'slug',
				'options',
				'taxonomies'
			);

			// If the variable is not a reserved variable
			if (!in_array($var, $reserved))

				// Write variable and value
				$this->$var = $value;
		}

	    /**
	     * Get slug
	     *
	     * Creates an url friendly slug.
	     *
	     * @param  string $name Name to slugify.
	     * @return string $name Returns the slug.
	     */
	    public function getSlug($name = null)
	    {
	        // If no name set use the post type name.
	        if (!isset($name))
	            $name = $this->postTypeName;

	        // Name to lower case.
	        $name = strtolower($name);

	        // Replace spaces with hyphen.
	        $name = str_replace(" ", "-", $name);

	        // Replace underscore with hyphen.
	        $name = str_replace("_", "-", $name);

	        return $name;
	    }

	    /**
	     * Get plural
	     *
	     * Returns the friendly plural name.
	     *
	     * @param  string $name The slug name you want to pluralize.
	     * @return string the friendly pluralized name.
	     */
	    public function getPlural($name = null)
	    {
	        // If no name is passed the post_type_name is used.
	        if (!isset($name))
	            $name = $this->postTypeName;

	        // Return the plural name. Add 's' to the end.
	        return $this->getHumanFriendly($name) . 's';
	    }

	    /**
	     * Get singular
	     *
	     * Returns the friendly singular name.
	     *
	     * @param string $name The slug name you want to unpluralize.
	     * @return string The friendly singular name.
	     */
	    public function getSingular($name = null)
	    {
	        // If no name is passed the postTypeName is used.
	        if (!isset($name))
	            $name = $this->postTypeName;

	        // Return the string.
	        return $this->getHumanFriendly($name);
	    }

	    /**
	     * Get human friendly
	     *
	     * Returns the human friendly name.
	     *
	     * @param string $name The name you want to make friendly.
	     * @return string The human friendly name.
	     */
	    public function getHumanFriendly($name = null)
	    {
	        // If no name is passed the postTypeName is used.
	        if (!isset($name))
	            $name = $this->postTypeName;

	        // Return human friendly name.
	        return ucwords(strtolower(str_replace("-", " ", str_replace("_", " ", $name))));
	    }

	    /**
	     * Register Post Type
	     *
	     * @see http://codex.wordpress.org/public function_Reference/registerPostType
	     */
	    public function registerPostType()
	    {
	        // Friendly post type names.
	        $plural   = $this->plural;
	        $singular = $this->singular;
	        $slug     = $this->slug;

	        // Default labels.
	        $labels = array(
	            'name'               => sprintf(__('%s', $this->textdomain), $plural),
	            'singular_name'      => sprintf(__('%s', $this->textdomain), $singular),
	            'menu_name'          => sprintf(__('%s', $this->textdomain), $plural),
	            'all_items'          => sprintf(__('%s', $this->textdomain), $plural),
	            'add_new'            => __('Add New', $this->textdomain),
	            'add_new_item'       => sprintf(__('Add New %s', $this->textdomain), $singular),
	            'edit_item'          => sprintf(__('Edit %s', $this->textdomain), $singular),
	            'new_item'           => sprintf(__('New %s', $this->textdomain), $singular),
	            'view_item'          => sprintf(__('View %s', $this->textdomain), $singular),
	            'search_items'       => sprintf(__('Search %s', $this->textdomain), $plural),
	            'not_found'          => sprintf(__('No %s found', $this->textdomain), $plural),
	            'not_found_in_trash' => sprintf(__('No %s found in Trash', $this->textdomain), $plural),
	            'parent_item_colon'  => sprintf(__('Parent %s:', $this->textdomain), $singular)
	        );

	        // Default options.
	        $defaults = array(
	            'labels'				=> $labels,
	            'hierarchical'        	=> false,
				'description'         	=> 'description',
				'taxonomies'          	=> array(),
				'public'              	=> true,
				'show_ui'             	=> true,
				'show_in_menu'        	=> true,
				'show_in_admin_bar'   	=> true,
				'menu_position'       	=> null,
				'menu_icon'           	=> null,
				'show_in_nav_menus'   	=> true,
				'publicly_queryable'  	=> true,
				'exclude_from_search' 	=> false,
				'has_archive'         	=> true,
				'query_var'           	=> true,
				'can_export'          	=> true,
				'capability_type'     	=> 'post',
				'supports'            	=> array(
					'title', 'editor', 'author', 'thumbnail',
					'excerpt','custom-fields', 'trackbacks', 'comments',
					'revisions', 'page-attributes', 'post-formats'
				),
	            'rewrite'				=> array(
	            	'slug' 			=> $slug,
	            	'with_front'	=> false,
	            ),
	        );

	        // Merge user submitted options with defaults.
	        $options = array_replace_recursive($defaults, $this->options);

	        // Set the object options as full options passed.
	        $this->options = $options;

	        // Check that the post type doesn't already exist.
	        if (!post_type_exists($this->postTypeName))

	            // Register the post type.
	            register_post_type($this->postTypeName, $options);
	    }

	    /**
	     * Dashboard glance
	     *
	     * @see  https://developer.wordpress.org/reference/hooks/dashboard_glance_items
	     */
	    public function dashboadGlance()
	    {
	    	// get number of published posts
	    	$numberOfPosts = number_format_i18n(wp_count_posts($this->postTypeName)->publish);

			// render CPT number
			echo '<li class="post-count"><a href="edit.php?post_type='.$this->postTypeName.'">'.$numberOfPosts.' '._n($this->singular, $this->plural, intval($numberOfPosts), $this->textdomain).'</a></li>';
	    }

		/**
		 * Recent Activity
		 *
		 * @param  array $queryArgs The current arguments array for displaying recent activity
		 * @return array 			 Modified arguments array to include this post type
		 */
		public function dashboardRecentPosts($queryArgs)
		{
			if (!is_array($queryArgs['post_type']))
					$queryArgs['post_type'] = array($queryArgs['post_type'], $this->postTypeName);
				else
					$queryArgs['post_type'][] = $this->postTypeName;

			return $queryArgs;
		}

	    /**
	     * Register taxonomy
	     *
	     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
	     *
	     * @param string $taxonomyName The slug for the taxonomy.
	     * @param array  $options Taxonomy options.
	     */
	    public function registerTaxonomy($taxonomyName, $options = array())
	    {
	        // Post type defaults to $this post type if unspecified.
	        $postType = $this->postTypeName;

	        // An array of the names required excluding taxonomyName.
	        $names = array(
	            'singular',
	            'plural',
	            'slug'
	        );

	        // if an array of names are passed
	        if (is_array($taxonomyNames)):

		            // Set the taxonomy name
		            $taxonomyName = $taxonomyNames['taxonomy_name'];

		            // Cycle through possible names.
		            foreach ($names as $name):

		                // If the user has set the name.
		                if (isset($taxonomyNames[$name])):

			                    // Use user submitted name.
			                    $$name = $taxonomyNames[$name];

		                    // Else generate the name.
		                	else:

			                    // Define the public function to be used.
			                    $method = 'get_' . $name;

			                    // Generate the name
			                    $$name = $this->$method($taxonomyName);

		                endif;
		            endforeach;

	            // Else if only the taxonomyName has been supplied.
	        	else:

		            // Create user friendly names.
		            $taxonomyName 	= $taxonomyNames;
		            $singular 		= $this->getSingular($taxonomyName);
		            $plural   		= $this->getPlural($taxonomyName);
		            $slug     		= $this->getSlug($taxonomyName);

			endif;

	        // Default labels.
	        $labels = array(
	            'name'                       => sprintf(__('%s', $this->textdomain), $plural),
	            'singular_name'              => sprintf(__('%s', $this->textdomain), $singular),
	            'menu_name'                  => sprintf(__('%s', $this->textdomain), $plural),
	            'all_items'                  => sprintf(__('All %s', $this->textdomain), $plural),
	            'edit_item'                  => sprintf(__('Edit %s', $this->textdomain), $singular),
	            'view_item'                  => sprintf(__('View %s', $this->textdomain), $singular),
	            'update_item'                => sprintf(__('Update %s', $this->textdomain), $singular),
	            'add_new_item'               => sprintf(__('Add New %s', $this->textdomain), $singular),
	            'new_item_name'              => sprintf(__('New %s Name', $this->textdomain), $singular),
	            'parent_item'                => sprintf(__('Parent %s', $this->textdomain), $plural),
	            'parent_item_colon'          => sprintf(__('Parent %s:', $this->textdomain), $plural),
	            'search_items'               => sprintf(__('Search %s', $this->textdomain), $plural),
	            'popular_items'              => sprintf(__('Popular %s', $this->textdomain), $plural),
	            'separate_items_with_commas' => sprintf(__('Seperate %s with commas', $this->textdomain), $plural),
	            'add_or_remove_items'        => sprintf(__('Add or remove %s', $this->textdomain), $plural),
	            'choose_from_most_used'      => sprintf(__('Choose from most used %s', $this->textdomain), $plural),
	            'not_found'                  => sprintf(__('No %s found', $this->textdomain), $plural),
	        );

	        // Default options.
	        $defaults = array(
	            'labels' 		=> $labels,
	            'hierarchical' 	=> true,
	            'rewrite' 		=> array('slug' => $slug)
	        );

	        // Merge default options with user submitted options.
	        $options = array_replace_recursive($defaults, $options);

	        // Add the taxonomy to the object array, this is used to add columns and filters to admin panel.
	        $this->taxonomies[] = $taxonomyName;

	        // Create array used when registering taxonomies.
	        $this->taxonomySettings[$taxonomyName] = $options;
	    }

	    /**
	     * Register taxonomies
	     *
	     * Cycles through taxonomies added with the class and registers them.
	     */
	    public function registerTaxonomies()
	    {
	        if (is_array($this->taxonomySettings)):

	            // Foreach taxonomy registered with the post type.
	            foreach ($this->taxonomySettings as $taxonomyName => $options):

	                // Register the taxonomy if it doesn't exist.
	                if (!taxonomy_exists($taxonomyName))

		                    // Register the taxonomy with Wordpress
		                    $this->registerTaxonomy($taxonomyName, $this->postTypeName, $options);

	                	else

		                    // If taxonomy exists, register it later with registerExisitingTaxonomies
		                    $this->existingTaxonomies[] = $taxonomyName;

	            endforeach;
	        endif;
	    }

	    /**
	     * Register Exisiting Taxonomies
	     *
	     * Cycles through exisiting taxonomies and registers them after the post type has been registered
	     */
	    public function registerExisitingTaxonomies()
	    {
	        if (is_array($this->existingTaxonomies))
	            foreach ($this->existingTaxonomies as $taxonomyName)
	                register_taxonomy_for_object_type($taxonomyName, $this->postTypeName);
	    }

	    /**
	     * Add admin columns
	     *
	     * Adds columns to the admin edit screen. public function is used with add_action
	     *
	     * @param array $columns Columns to be added to the admin edit screen.
	     * @return array
	     */
	    public function addAdminColumns($columns)
	    {
	        // If no user columns have been specified, add taxonomies
	        if (!isset($this->columns)):

		            $newColumns = array();

		            // determine which column to add custom taxonomies after
		            if (is_array($this->taxonomies) && in_array('post_tag', $this->taxonomies) || $this->postTypeName === 'post')
		    	            $after = 'tags';
		            	elseif (is_array($this->taxonomies) && in_array('category', $this->taxonomies) || $this->postTypeName === 'post')
		                		$after = 'categories';
		            		elseif (post_type_supports($this->postTypeName, 'author'))
		                			$after = 'author';
		            			else
		 			               $after = 'title';

		            // foreach exisiting columns
		            foreach ($columns as $key => $title):

		                // add exisiting column to the new column array
		                $newColumns[$key] = $title;

		                // we want to add taxonomy columns after a specific column
		                if ($key === $after):

		                    // If there are taxonomies registered to the post type.
		                    if (is_array($this->taxonomies)):

		                        // Create a column for each taxonomy.
		                        foreach ($this->taxonomies as $tax):

		                            // WordPress adds Categories and Tags automatically, ignore these
		                            if ($tax !== 'category' && $tax !== 'post_tag'):

		                                // Get the taxonomy object for labels.
		                                $taxonomy_object = get_taxonomy($tax);

		                                // Column key is the slug, value is friendly name.
		                                $newColumns[$tax] = sprintf(__('%s', $this->textdomain), $taxonomy_object->labels->name);

		                            endif;
		                        endforeach;
		                    endif;
		                endif;
		            endforeach;

		            // overide with new columns
		            $columns = $newColumns;

		        else:

					// Use user submitted columns, these are defined using the object columns() method.
					$columns = $this->columns;
	        endif;

	        return $columns;
	    }

	    /**
	     * Populate admin columns
	     *
	     * Populate custom columns on the admin edit screen.
	     *
	     * @param string $column The name of the column.
	     * @param integer $post_id The post ID.
	     */
	    public function populateAdminColumns($column, $post_id)
	    {
	        // Get wordpress $post object.
	        global $post;

	        // determine the column
	        switch ($column):

	            // If column is a taxonomy associated with the post type.
	            case (taxonomy_exists($column)):

	                // Get the taxonomy for the post
	                $terms = get_the_terms($post_id, $column);

	                // If we have terms.
	                if (!empty($terms)):

		                    $output = array();

		                    // Loop through each term, linking to the 'edit posts' page for the specific term.
		                    foreach ($terms as $term):

		                        // Output is an array of terms associated with the post.
		                        $output[] = sprintf(

		                            // Define link.
		                            '<a href="%s">%s</a>',

		                            // Create filter url.
		                            esc_url(add_query_arg(array('post_type' => $post->post_type, $column => $term->slug), 'edit.php')),

		                            // Create friendly term name.
		                            esc_html(sanitize_term_field('name', $term->name, $term->term_id, $column, 'display'))
		                        );

		                    endforeach;

		                    // Join the terms, separating them with a comma.
		                    echo join(', ', $output);

		                // If no terms found.
		                else:

    	                    // Get the taxonomy object for labels
    	                    $taxonomy_object = get_taxonomy($column);

    	                    // Echo no terms.
    	                    printf(__('No %s', $this->textdomain), $taxonomy_object->labels->name);
	                endif;
	            	break;

	            // If column is for the post ID.
	            case 'post_id':
	                echo $post->ID;
		            break;

	            // if the column is prepended with 'meta_', this will automagically retrieve the meta values and display them.
	            case (preg_match('/^meta_/', $column) ? true : false):

	                // meta_book_author (meta key = book_author)
	                $x = substr($column, 5);
	                $meta = get_post_meta($post->ID, $x);
	                echo join(", ", $meta);
		            break;

	            // If the column is post thumbnail.
	            case 'icon':

	                // Create the edit link.
	                $link = esc_url(add_query_arg(array('post' => $post->ID, 'action' => 'edit'), 'post.php'));

	                // If it post has a featured image.
	                if (has_post_thumbnail()):

		                    // Display post featured image with edit link.
		                    echo '<a href="'.$link.'">';
		                        the_post_thumbnail(array(60, 60));
		                    echo '</a>';

	                	else:

		                    // Display default media image with link.
		                    echo '<a href="'.$link.'"><img src="'. site_url('/wp-includes/images/crystal/default.png').'" alt="'.$post->post_title .'" /></a>';
	                endif;
		            break;

	            // Default case checks if the column has a user public function, this is most commonly used for custom fields.
	            default:

	                // If there are user custom columns to populate.
	                if (isset($this->customPopulateColumns) && is_array($this->customPopulateColumns))

	                    // If this column has a user submitted public function to run.
	                    if (isset($this->customPopulateColumns[$column]) && is_callable($this->customPopulateColumns[$column]))

	                        // Run the public function.
	                        call_user_func_array($this->customPopulateColumns[$column], array($column, $post));
		            break;
	        endswitch; // end switch ($column)
	    }

	    /**
	     * Filters
	     *
	     * User public function to define which taxonomy filters to display on the admin page.
	     *
	     * @param array $filters An array of taxonomy filters to display.
	     */
	    public function filters($filters = array())
	    {
	        $this->filters = $filters;
	    }

	    /**
	     *  Add taxtonomy filters
	     *
	     * Creates select fields for filtering posts by taxonomies on admin edit screen.
	    */
	    public function addTaxonomyFilters()
	    {
	        global $typenow;
	        global $wp_query;

	        // Must set this to the post type you want the filter(s) displayed on.
	        if ($typenow == $this->postTypeName):

	            // if custom filters are defined use those
	            if (is_array($this->filters))
		                $filters = $this->filters;

		            // else default to use all taxonomies associated with the post
		            else
		                $filters = $this->taxonomies;

	            if (!empty($filters)):

	                // Foreach of the taxonomies we want to create filters for...
	                foreach ($filters as $tax_slug):

	                    // ...object for taxonomy, doesn't contain the terms.
	                    $tax = get_taxonomy($tax_slug);

	                    // Get taxonomy terms and order by name.
	                    $args = array(
	                        'orderby' 		=> 'name',
	                        'hide_empty' 	=> false
	                    );

	                    // Get taxonomy terms.
	                    $terms = get_terms($tax_slug, $args);

	                    // If we have terms.
	                    if ($terms):

	                        // Set up select box.
	                        printf(' &nbsp;<select name="%s" class="postform">', $tax_slug);

	                        // Default show all.
	                        printf('<option value="0">%s</option>', sprintf(__('Show all %s', $this->textdomain), $tax->label));

	                        // Foreach term create an option field ...
	                        foreach ($terms as $term):

	                            // ... if filtered by this term make it selected.
	                            if (isset($_GET[$tax_slug]) && $_GET[$tax_slug] === $term->slug)
	                                	printf('<option value="%s" selected="selected">%s (%s)</option>', $term->slug, $term->name, $term->count);

		                            // ... create option for taxonomy.
		                            else
		                                printf('<option value="%s">%s (%s)</option>', $term->slug, $term->name, $term->count);

	                        endforeach; // foreach ($terms as $term)

	                        // End the select field.
	                        print('</select>&nbsp;');
	                    endif; // if ($terms)
	                endforeach; // foreach ($filters as $tax_slug)
	            endif; // if (!empty($filters))
	        endif; // if ($typenow == $this->postTypeName)
	    }

	    /**
	     * Columns
	     *
	     * Choose columns to be displayed on the admin edit screen.
	     *
	     * @param array $columns An array of columns to be displayed.
	     */
	    public function columns($columns)
	    {
	        // If columns is set.
	        if (isset($columns))

	            // Assign user submitted columns to object.
	            $this->columns = $columns;
	    }

	    /**
	     * Populate columns
	     *
	     * Define what and how to populate a speicific admin column.
	     *
	     * @param string $columnName The name of the column to populate.
	     * @param mixed $callback An anonyous public function or callable array to call when populating the column.
	     */
	    public function populateColumn($columnName, $callback)
	    {
	        $this->customPopulateColumns[$columnName] = $callback;
	    }

	    /**
	     * Sortable
	     *
	     * Define what columns are sortable in the admin edit screen.
	     *
	     * @param array $columns An array of columns that are sortable.
	     */
	    public function sortable($columns = array())
	    {
	        // Assign user defined sortable columns to object variable.
	        $this->sortable = $columns;

	        // Run filter to make columns sortable.
	        add_filter('manage_edit-'.$this->postTypeName.'_sortable_columns', array(&$this, 'makeColumnsSortable'));

	        // Run action that sorts columns on request.
	        add_action('load-edit.php', array(&$this, 'loadEdit'));
	    }

	    /**
	     * Make columns sortable
	     *
	     * Internal public function that adds user defined sortable columns to WordPress default columns.
	     *
	     * @param array $columns Columns to be sortable.
	     *
	     */
	    public function makeColumnsSortable($columns)
	    {
	        // For each sortable column.
	        foreach ($this->sortable as $column => $values):

	            // Make an array to merge into wordpress sortable columns.
	            $sortable_columns[$column] = $values[0];
	        endforeach;

	        // Merge sortable columns array into wordpress sortable columns.
	        $columns = array_merge($sortable_columns, $columns);

	        return $columns;
	    }

	    /**
	     * Load edit
	     *
	     * Sort columns only on the edit.php page when requested.
	     *
	     * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/request
	     */
	    public function loadEdit()
	    {
	        // Run filter to sort columns when requested
	        add_filter('request', array(&$this, 'sortColumns'));
	    }

	    /**
	     * Sort columns
	     *
	     * Internal public function that sorts columns on request.
	     *
	     * @see loadEdit()
	     *
	     * @param array $vars The query vars submitted by user.
	     * @return array A sorted array.
	     */
	    public function sortColumns($vars)
	    {
	        // Cycle through all sortable columns submitted by the user
	        foreach ($this->sortable as $column => $values):

	            // Retrieve the meta key from the user submitted array of sortable columns
	            $metaKey = $values[0];

	            // If the meta_key is a taxonomy
	            if (taxonomy_exists($metaKey)):

		                // Sort by taxonomy.
		                $key = "taxonomy";

		            else:

		                // else by meta key.
		                $key = "meta_key";
	            endif;

	            // If the optional parameter is set and is set to true
	            if (isset($values[1]) && true === $values[1]):

		                // Vaules needed to be ordered by integer value
		                $orderby = 'meta_value_num';

		            else:

		                // Values are to be order by string value
		                $orderby = 'meta_value';
		        endif;

	            // Check if we're viewing this post type
	            if (isset($vars['post_type']) && $this->postTypeName == $vars['post_type']):

	                // find the meta key we want to order posts by
	                if (isset($vars['orderby']) && $metaKey == $vars['orderby']):

	                    // Merge the query vars with our custom variables
	                    $vars = array_merge(
	                        $vars,
	                        array(
	                            'meta_key' 	=> $metaKey,
	                            'orderby' 	=> $orderby
	                        )
	                    );
	                endif;
	            endif;
	        endforeach; // foreach ($this->sortable as $column => $values)

	        return $vars;
	    }

	    /**
	     * Set menu icon
	     *
	     * Use this public function to set the menu icon in the admin dashboard. Since WordPress v3.8
	     * dashicons are used. For more information see @link https://developer.wordpress.org/resource/dashicons/
	     *
	     * @param string $icon dashicon name
	     */
	    public function menuIcon($icon = "dashicons-admin-page")
	    {
	        if (is_string($icon) && stripos($icon, "dashicons") !== false)
		            $this->options["menu_icon"] = $icon;
	        	else
		            // Set a default menu icon
		            $this->options["menu_icon"] = "dashicons-admin-page";
	    }

	    /**
	     * Set textdomain
	     *
	     * @param string $textdomain Textdomain used for translation.
	     */
	    public function setTextdomain($textdomain)
	    {
	        $this->textdomain = $textdomain;
	    }

	    /**
	     * Updated messages
	     *
	     * Internal public function that modifies the post type names in updated messages
	     *
	     * @param array $messages an array of post updated messages
	     */
	    public function updatedMessages($messages)
	    {
	        $post = get_post();
	        $singular = $this->singular;

	        $messages[$this->postTypeName] = array(
	            0 => '',
	            1 => sprintf(__('%s updated.', $this->textdomain), $singular),
	            2 => __('Custom field updated.', $this->textdomain),
	            3 => __('Custom field deleted.', $this->textdomain),
	            4 => sprintf(__('%s updated.', $this->textdomain), $singular),
	            5 => isset($_GET['revision']) ? sprintf(__('%2$s restored to revision from %1$s', $this->textdomain), wp_post_revision_title((int) $_GET['revision'], false), $singular) : false,
	            6 => sprintf(__('%s updated.', $this->textdomain), $singular),
	            7 => sprintf(__('%s saved.', $this->textdomain), $singular),
	            8 => sprintf(__('%s submitted.', $this->textdomain), $singular),
	            9 => sprintf(
	                __('%2$s scheduled for: <strong>%1$s</strong>.', $this->textdomain),
	                date_i18n(__('M j, Y @ G:i', $this->textdomain), strtotime($post->post_date)),
	                $singular
	            ),
	            10 => sprintf(__('%s draft updated.', $this->textdomain), $singular),
	        );

	        return $messages;
	    }

	    /**
	     * Bulk updated messages
	     *
	     * Internal public function that modifies the post type names in bulk updated messages
	     *
	     * @param array $messages an array of bulk updated messages
	     */
	    public function bulkUpdatedMessages($bulkMessages, $bulkCounts)
	    {
	        $singular = $this->singular;
	        $plural = $this->plural;

	        $bulkMessages[$this->postTypeName] = array(
	            'updated'   => _n('%s '.$singular.' updated.', '%s '.$plural.' updated.', $bulkCounts['updated']),
	            'locked'    => _n('%s '.$singular.' not updated, somebody is editing it.', '%s '.$plural.' not updated, somebody is editing them.', $bulkCounts['locked']),
	            'deleted'   => _n('%s '.$singular.' permanently deleted.', '%s '.$plural.' permanently deleted.', $bulkCounts['deleted']),
	            'trashed'   => _n('%s '.$singular.' moved to the Trash.', '%s '.$plural.' moved to the Trash.', $bulkCounts['trashed']),
	            'untrashed' => _n('%s '.$singular.' restored from the Trash.', '%s '.$plural.' restored from the Trash.', $bulkCounts['untrashed']),
	        );

	        return $bulkMessages;
	    }

	    /**
	     * Flush
	     *
	     * Flush rewrite rules programatically
	     */
	    public function flush()
	    {
	        flush_rewrite_rules();
	    }
    }

?>
