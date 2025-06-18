<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// We need to include the Parsedown library.
// Make sure the Parsedown.php file is in an 'includes' folder within your plugin.
require_once __DIR__ . '/../includes/Parsedown.php';


/**
 * Elementor Markdown Widget.
 *
 * An Elementor widget that parses and displays content written in Markdown.
 *
 * @since 1.0.0
 */
class Elementor_Markdown_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget name.
     */
    public function get_name() {
        return 'markdown_widget';
    }

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Markdown', 'elementor-markdown-widget' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-code'; // Elementor has a built-in icon library
    }

    /**
     * Get widget categories.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'custom-widgets' ];
    }
    
    /**
     * Get widget keywords.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget keywords.
     */
    public function get_keywords() {
		return [ 'markdown', 'code', 'text' ];
	}

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {

        // --- Content Section ---
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Markdown Content', 'elementor-markdown-widget' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'markdown_content',
            [
                'label' => esc_html__( 'Markdown', 'elementor-markdown-widget' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 20,
                'placeholder' => esc_html__( 'Paste your Markdown here...', 'elementor-markdown-widget' ),
                'default' => esc_html__( '## Hello World!'. "\n\n" .'- This is a list item.'."\n" .'- **Bold text** and *italic text*.'."\n\n" .'[Elementor Website](https://elementor.com)', 'elementor-markdown-widget made by Raefko for FuzzingLabs' ),
            ]
        );

        $this->end_controls_section();
        
        // --- Style Section ---
        $this->start_controls_section(
			'style_section',
			[
				'label' => esc_html__( 'Typography', 'elementor-markdown-widget' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .markdown-output',
			]
		);

		$this->end_controls_section();

    }

    /**
     * Render widget output on the frontend.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $markdown_text = $settings['markdown_content'];

        if ( empty( $markdown_text ) ) {
            return;
        }

        // Initialize Parsedown
        $Parsedown = new Parsedown();
        
        // Sanitize untrusted HTML. If you fully trust your content, you can remove this.
        $Parsedown->setSafeMode(true); 

        // Convert Markdown to HTML
        $html = $Parsedown->text( $markdown_text );
        
        // Output the HTML
        echo '<div class="markdown-output">' . $html . '</div>';
    }
    
    /**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
        // We can't run PHP here, so we'll just display the raw text or a placeholder.
        // The actual markdown parsing happens on the frontend view (the render() method).
        // For a live preview, a more complex AJAX solution would be needed.
        #>
		<div class="markdown-output">
            {{{ settings.markdown_content.replace(/\n/g, '<br>') }}}
        </div>
		<?php
	}
}