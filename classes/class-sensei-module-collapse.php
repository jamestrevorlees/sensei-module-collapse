<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Sensei_Module_Collapse {
    private $dir;
    private $file;
    private $assets_dir;
    private $assets_url;
    private $order_page_slug;
    public $taxonomy;

    public function __construct( $file )
    {
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));
        $this->taxonomy = 'collapse';
        $this->order_page_slug = 'module-collapse';

        // Enque CSS and JS scripts
        add_action( 'sensei_single_course_modules_content' , array( $this, 'enqueue_module_collapse_scripts' ) , 10 );

        // Remove native Sensei module title and content display
        add_action('sensei_single_course_modules_before', 'mod_title_remove_action'); // prioroty of 1, but can be anything higher (lower number) then the priority of the action

        function mod_title_remove_action() {
            global $woothemes_sensei;
            remove_action( 'sensei_single_course_modules_before', array($woothemes_sensei->modules, 'course_modules_title'),20);
          }
        add_action('sensei_single_course_modules_content', 'mod_content_remove_action'); // prioroty of 1, but can be anything higher (lower number) then the priority of the action

        function mod_content_remove_action() {
            global $woothemes_sensei;
            remove_action( 'sensei_single_course_modules_content', array( $woothemes_sensei->modules, 'course_module_content' ),20);
        }

        // Add collapsible module title and content display
        add_action('sensei_single_course_modules_before',array( $this,'new_course_modules_title' ), 21);
        add_action('sensei_single_course_modules_content', array( $this,'course_module_content' ), 20);

    }

	/**
	 * Load admin JS
	 * @return void
	 */
	public function enqueue_module_collapse_scripts () {
		global $wp_version;

		if( $wp_version >= 3.5 ) {
            wp_enqueue_style('module-collapse',  $this->assets_url . 'css/sensei-module-collapse.css' , '1.0.0');
            wp_register_script('module-collapsed',  $this->assets_url . 'js/sensei-module-collapse.js' , array(),
                '1.0',
                true);
            wp_enqueue_script('module-collapsed');
		}

	}


    /**
     * Show the title modules on the single course template with Collapse All/Expand All links.
     *
     * Function is hooked into sensei_single_course_modules_before.
     *
     * @since 1.8.0
     * @return void
     */
    public function new_course_modules_title( ) {
        echo '<header><h2>' . __('Modules', 'woothemes-sensei') . '</h2></header>
        <div class="listControl"><a class="expandList">Expand All</a> | <a class="collapseList">Collapse All</a></div></br>';

    }

    /**
     * Display the single course modules content with Collapse/Expand Toggle
     *
     * @since 1.8.0
     * @return void
     */
    public function course_module_content(){
        global $post;
        $course_id = $post->ID;
        $modules = Sensei()->modules->get_course_modules( $course_id  );

        // Display each module
        foreach ($modules as $module) {
            echo '<article class="post module">';
            echo '<section class="entry">';
            $module_progress = false;
            if (is_user_logged_in()) {
                global $current_user;
                wp_get_current_user();
                $module_progress = Sensei()->modules->get_user_module_progress($module->term_id, $course_id, $current_user->ID);
            }

            if ($module_progress && $module_progress > 0) {
                $status = __('Completed', 'woothemes-sensei');
                $class = 'completed';
                if ($module_progress < 100) {
                    $status = __('In progress', 'woothemes-sensei');
                    $class = 'in-progress';
                }
                echo '<p class="status module-status ' . esc_attr($class) . '">' . $status . '</p>';
            }

            if ('' != $module->description) {
                echo '<p class="module-description">' . $module->description . '</p>';
            }

            $lessons = Sensei()->modules->get_lessons( $course_id ,$module->term_id );

            if (count($lessons) > 0) {

                $lessons_list = '';
                foreach ($lessons as $lesson) {
                    $status = '';
                    $lesson_completed = WooThemes_Sensei_Utils::user_completed_lesson($lesson->ID, get_current_user_id() );
                    $title = esc_attr(get_the_title(intval($lesson->ID)));

                    if ($lesson_completed) {
                        $status = 'completed';
                    }

                    $lessons_list .= '<li class="' . $status . '"><a href="' . esc_url(get_permalink(intval($lesson->ID))) . '" title="' . esc_attr(get_the_title(intval($lesson->ID))) . '">' . apply_filters('sensei_module_lesson_list_title', $title, $lesson->ID) . '</a></li>';

                    // Build array of displayed lesson for exclusion later
                    $displayed_lessons[] = $lesson->ID;
                }

                ?>

                <section class="module-lessons">
                    <ul >
                        <header class="expList">
                            <?php
                            // module title header with collapsing toggle
                            $module_url = esc_url(add_query_arg('course_id', $course_id, get_term_link($module, $this->taxonomy)));
                            echo "<img class='expList' src='" . $this->assets_url ."img/collapse.png' ><h2 class='expList'> " . $module->name . "</h2>"; ?>
                        </header>
                        <li >
                            <ul class="expList2" >
                                <?php echo $lessons_list; ?>
                            </ul>
                        </li>
                    </ul>
                </section>

            <?php }//end count lessons  ?>
                </section>
            </article>
        <?php

        } // end each module

    } // end course_module_content

}