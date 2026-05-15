<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
	return;
}

class XWoo_Filter_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'xwoo-filter';
	}

	public function get_title() {
		return esc_html__( 'XWoo Filter', 'prdctfltr' );
	}

	public function get_icon() {
		return 'eicon-filter';
	}

	public function get_categories() {
		return array( 'woocommerce-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'filter', 'product', 'woocommerce', 'xforwoocommerce', 'prdctfltr', 'xwoo' );
	}

	public function get_style_depends() {
		return array( 'prdctfltr', 'xwoo-filter' );
	}

	public function get_script_depends() {
		return array( 'prdctfltr-main-js', 'xwoo-filter' );
	}

	private function get_preset_options() {
		$options = array( 'default' => esc_html__( 'Default', 'prdctfltr' ) );

		if ( function_exists( 'Prdctfltr' ) ) {
			$presets = Prdctfltr()->__get_presets();
			if ( is_array( $presets ) ) {
				foreach ( $presets as $preset ) {
					if ( isset( $preset['slug'] ) ) {
						$options[ $preset['slug'] ] = isset( $preset['name'] ) ? $preset['name'] : $preset['slug'];
					}
				}
			}
		}

		return $options;
	}

	protected function register_controls() {

		$this->section_content_filter();
		$this->section_content_layout();
		$this->section_content_sections();
		$this->section_content_trigger();

		$this->section_style_container();
		$this->section_style_section_headers();
		$this->section_style_options();
		$this->section_style_trigger();
		$this->section_style_drawer();
	}

	private function section_content_filter() {

		$this->start_controls_section(
			'section_filter',
			array(
				'label' => esc_html__( 'Filter Source', 'prdctfltr' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'preset',
			array(
				'label'       => esc_html__( 'Preset', 'prdctfltr' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $this->get_preset_options(),
				'default'     => 'default',
				'description' => esc_html__( 'Choose which filter preset to display. Manage presets under XforWooCommerce → Product Filter.', 'prdctfltr' ),
			)
		);

		$this->add_control(
			'style_mode',
			array(
				'label'   => esc_html__( 'Filter Style', 'prdctfltr' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'pf_inherit'        => esc_html__( 'Inherit from preset', 'prdctfltr' ),
					'pf_default_inline' => esc_html__( 'Flat inline', 'prdctfltr' ),
					'pf_default'        => esc_html__( 'Flat block', 'prdctfltr' ),
					'pf_default_select' => esc_html__( 'Flat select', 'prdctfltr' ),
				),
				'default' => 'pf_inherit',
			)
		);

		$this->add_control(
			'disable_overrides',
			array(
				'label'        => esc_html__( 'Disable preset manager overrides', 'prdctfltr' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();
	}

	private function section_content_layout() {

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'prdctfltr' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'display_mode',
			array(
				'label'       => esc_html__( 'Display Mode', 'prdctfltr' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'popup'      => esc_html__( 'Pop-up (centered modal)', 'prdctfltr' ),
					'drawer'     => esc_html__( 'Off-canvas drawer (slides in from side)', 'prdctfltr' ),
					'fullscreen' => esc_html__( 'Full-screen overlay', 'prdctfltr' ),
					'inline'     => esc_html__( 'Inline (in place)', 'prdctfltr' ),
				),
				'default'     => 'popup',
				'description' => esc_html__( 'All modes except inline hide the filter behind a button until clicked. Show Results / X / backdrop / Esc all close the overlay.', 'prdctfltr' ),
			)
		);

		$this->add_responsive_control(
			'popup_width',
			array(
				'label'      => esc_html__( 'Pop-up Width', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 280, 'max' => 900 ),
					'%'  => array( 'min' => 30,  'max' => 100 ),
					'vw' => array( 'min' => 30,  'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 480 ),
				'condition'  => array( 'display_mode' => 'popup' ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-drawer.xwoo-mode-popup' => '--xwoo-popup-width: {{SIZE}}{{UNIT}};',
					'.xwoo-filter-drawer.xwoo-mode-popup[data-xwoo-owner="xwoo-filter-{{ID}}"]' => '--xwoo-popup-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_max_height',
			array(
				'label'      => esc_html__( 'Pop-up Max Height', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 300, 'max' => 1200 ),
					'%'  => array( 'min' => 40,  'max' => 100 ),
					'vh' => array( 'min' => 40,  'max' => 95 ),
				),
				'default'    => array( 'unit' => 'vh', 'size' => 85 ),
				'condition'  => array( 'display_mode' => 'popup' ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-drawer.xwoo-mode-popup' => '--xwoo-popup-max-h: {{SIZE}}{{UNIT}};',
					'.xwoo-filter-drawer.xwoo-mode-popup[data-xwoo-owner="xwoo-filter-{{ID}}"]' => '--xwoo-popup-max-h: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'drawer_position',
			array(
				'label'     => esc_html__( 'Drawer Position', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'prdctfltr' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'prdctfltr' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'left',
				'condition' => array( 'display_mode' => 'drawer' ),
			)
		);

		$this->add_responsive_control(
			'drawer_width',
			array(
				'label'      => esc_html__( 'Drawer Width', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array( 'min' => 240, 'max' => 800 ),
					'%'  => array( 'min' => 30,  'max' => 100 ),
					'vw' => array( 'min' => 30,  'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 380 ),
				'condition'  => array( 'display_mode' => 'drawer' ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-drawer' => '--xwoo-drawer-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'mobile_force_drawer',
			array(
				'label'        => esc_html__( 'Force drawer on mobile', 'prdctfltr' ),
				'description'  => esc_html__( 'Show inline on desktop, drawer on mobile.', 'prdctfltr' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'display_mode' => 'inline' ),
			)
		);

		$this->end_controls_section();
	}

	private function section_content_sections() {

		$this->start_controls_section(
			'section_sections',
			array(
				'label' => esc_html__( 'Sections', 'prdctfltr' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'collapsible',
			array(
				'label'        => esc_html__( 'Collapsible sections', 'prdctfltr' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'collapsed_by_default',
			array(
				'label'        => esc_html__( 'Start collapsed', 'prdctfltr' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'collapsible' => 'yes' ),
			)
		);

		$this->add_control(
			'single_open',
			array(
				'label'        => esc_html__( 'Accordion (only one open at a time)', 'prdctfltr' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'collapsible' => 'yes' ),
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'      => esc_html__( 'Animation Speed (ms)', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 800, 'step' => 25 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 220 ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter' => '--xwoo-anim: {{SIZE}}ms;',
				),
				'condition'  => array( 'collapsible' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	private function section_content_trigger() {

		$this->start_controls_section(
			'section_trigger',
			array(
				'label'     => esc_html__( 'Trigger Button', 'prdctfltr' ),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => array( 'display_mode!' => 'inline' ),
			)
		);

		$this->add_control(
			'trigger_text',
			array(
				'label'       => esc_html__( 'Button Text', 'prdctfltr' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Filters', 'prdctfltr' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'trigger_icon',
			array(
				'label'   => esc_html__( 'Icon', 'prdctfltr' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'eicon-filter',
					'library' => 'eicons',
				),
			)
		);

		$this->add_control(
			'trigger_icon_position',
			array(
				'label'   => esc_html__( 'Icon Position', 'prdctfltr' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'before' => array(
						'title' => esc_html__( 'Before', 'prdctfltr' ),
						'icon'  => 'eicon-h-align-left',
					),
					'after'  => array(
						'title' => esc_html__( 'After', 'prdctfltr' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'before',
			)
		);

		$this->add_responsive_control(
			'trigger_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 10, 'max' => 64 ),
					'em' => array( 'min' => 0.5, 'max' => 4, 'step' => 0.1 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 20 ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-trigger-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .xwoo-filter-trigger-icon svg, {{WRAPPER}} .xwoo-filter-trigger-icon i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'trigger_show_count',
			array(
				'label'        => esc_html__( 'Show active filter count', 'prdctfltr' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_responsive_control(
			'trigger_align',
			array(
				'label'     => esc_html__( 'Alignment', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start'  => array(
						'title' => esc_html__( 'Left', 'prdctfltr' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'      => array(
						'title' => esc_html__( 'Center', 'prdctfltr' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'    => array(
						'title' => esc_html__( 'Right', 'prdctfltr' ),
						'icon'  => 'eicon-text-align-right',
					),
					'stretch'     => array(
						'title' => esc_html__( 'Stretch', 'prdctfltr' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-trigger-wrap' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .xwoo-filter-trigger-wrap.xwoo-stretch .xwoo-filter-trigger' => 'width: 100%;',
				),
			)
		);

		$this->end_controls_section();
	}

	private function section_style_container() {

		$this->start_controls_section(
			'style_container',
			array(
				'label' => esc_html__( 'Container', 'prdctfltr' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'container_bg',
			array(
				'label'     => esc_html__( 'Background', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .xwoo-filter',
			)
		);

		$this->add_responsive_control(
			'container_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_shadow',
				'selector' => '{{WRAPPER}} .xwoo-filter',
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function section_style_section_headers() {

		$this->start_controls_section(
			'style_section_headers',
			array(
				'label' => esc_html__( 'Section Headers', 'prdctfltr' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_typography',
				'selector' => '{{WRAPPER}} .xwoo-filter .pf-help-title .widget-title, {{WRAPPER}} .xwoo-filter .pf-help-title .prdctfltr_widget_title',
			)
		);

		$this->start_controls_tabs( 'header_state_tabs' );

		$this->start_controls_tab( 'header_normal', array( 'label' => esc_html__( 'Normal', 'prdctfltr' ) ) );

		$this->add_control(
			'header_color',
			array(
				'label'     => esc_html__( 'Text Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .pf-help-title .widget-title, {{WRAPPER}} .xwoo-filter .pf-help-title .prdctfltr_widget_title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_bg',
			array(
				'label'     => esc_html__( 'Background', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .pf-help-title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'header_hover', array( 'label' => esc_html__( 'Hover', 'prdctfltr' ) ) );

		$this->add_control(
			'header_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .pf-help-title:hover .widget-title, {{WRAPPER}} .xwoo-filter .pf-help-title:hover .prdctfltr_widget_title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_bg_hover',
			array(
				'label'     => esc_html__( 'Background', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .pf-help-title:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'header_padding',
			array(
				'label'      => esc_html__( 'Padding', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter .pf-help-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_divider_color',
			array(
				'label'     => esc_html__( 'Divider Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .prdctfltr_filter' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'chevron_color',
			array(
				'label'     => esc_html__( 'Chevron Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .prdctfltr-down, {{WRAPPER}} .xwoo-chevron' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function section_style_options() {

		$this->start_controls_section(
			'style_options',
			array(
				'label' => esc_html__( 'Filter Options', 'prdctfltr' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'option_typography',
				'selector' => '{{WRAPPER}} .xwoo-filter .prdctfltr_checkboxes label, {{WRAPPER}} .xwoo-filter .prdctfltr_checkboxes label span',
			)
		);

		$this->add_control(
			'option_color',
			array(
				'label'     => esc_html__( 'Text Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .prdctfltr_checkboxes label, {{WRAPPER}} .xwoo-filter .prdctfltr_checkboxes label span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'option_color_hover',
			array(
				'label'     => esc_html__( 'Hover Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter .prdctfltr_checkboxes label:hover, {{WRAPPER}} .xwoo-filter .prdctfltr_checkboxes label:hover span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'       => esc_html__( 'Accent Color', 'prdctfltr' ),
				'description' => esc_html__( 'Selected checkbox, slider handle, active states.', 'prdctfltr' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'selectors'   => array(
					'{{WRAPPER}} .xwoo-filter' => '--xwoo-accent: {{VALUE}};',
					'{{WRAPPER}} .xwoo-filter input[type="checkbox"]:checked, {{WRAPPER}} .xwoo-filter input[type="radio"]:checked' => 'accent-color: {{VALUE}}; background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'option_gap',
			array(
				'label'      => esc_html__( 'Row Gap', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 30 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 6 ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter .prdctfltr_checkboxes label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function section_style_trigger() {

		$this->start_controls_section(
			'style_trigger',
			array(
				'label'     => esc_html__( 'Trigger Button', 'prdctfltr' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_mode!' => 'inline' ),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'trigger_typography',
				'selector' => '{{WRAPPER}} .xwoo-filter-trigger',
			)
		);

		$this->start_controls_tabs( 'trigger_state_tabs' );

		$this->start_controls_tab( 'trigger_normal', array( 'label' => esc_html__( 'Normal', 'prdctfltr' ) ) );

		$this->add_control(
			'trigger_color',
			array(
				'label'     => esc_html__( 'Text Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-trigger' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_bg',
			array(
				'label'     => esc_html__( 'Background', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-trigger' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'trigger_hover_tab', array( 'label' => esc_html__( 'Hover', 'prdctfltr' ) ) );

		$this->add_control(
			'trigger_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-trigger:hover, {{WRAPPER}} .xwoo-filter-trigger:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_bg_hover',
			array(
				'label'     => esc_html__( 'Background', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-trigger:hover, {{WRAPPER}} .xwoo-filter-trigger:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'trigger_border',
				'selector' => '{{WRAPPER}} .xwoo-filter-trigger',
			)
		);

		$this->add_responsive_control(
			'trigger_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_padding',
			array(
				'label'      => esc_html__( 'Padding', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'    => 12,
					'right'  => 20,
					'bottom' => 12,
					'left'   => 20,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function section_style_drawer() {

		$this->start_controls_section(
			'style_drawer',
			array(
				'label'     => esc_html__( 'Drawer / Overlay', 'prdctfltr' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_mode!' => 'inline' ),
			)
		);

		$this->add_control(
			'drawer_bg',
			array(
				'label'     => esc_html__( 'Drawer Background', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-drawer-panel' => 'background-color: {{VALUE}};',
					'.xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-drawer-panel' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'backdrop_color',
			array(
				'label'     => esc_html__( 'Backdrop Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.55)',
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-backdrop' => 'background-color: {{VALUE}};',
					'.xwoo-filter-backdrop[data-xwoo-owner="xwoo-filter-{{ID}}"]' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'backdrop_blur',
			array(
				'label'     => esc_html__( 'Backdrop Blur (px)', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 20 ) ),
				'default'   => array( 'unit' => 'px', 'size' => 4 ),
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-backdrop' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);',
					'.xwoo-filter-backdrop[data-xwoo-owner="xwoo-filter-{{ID}}"]' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_size',
			array(
				'label'      => esc_html__( 'Close Icon Size', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 18 ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-close svg, {{WRAPPER}} .xwoo-filter-close i' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
					'.xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close svg, .xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close i' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'close_button_size',
			array(
				'label'      => esc_html__( 'Close Button Size (tap target)', 'prdctfltr' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 24, 'max' => 64 ) ),
				'default'    => array( 'unit' => 'px', 'size' => 32 ),
				'selectors'  => array(
					'{{WRAPPER}} .xwoo-filter-close' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
					'.xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->start_controls_tabs( 'close_state_tabs' );

		$this->start_controls_tab( 'close_normal_tab', array( 'label' => esc_html__( 'Normal', 'prdctfltr' ) ) );

		$this->add_control(
			'close_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-close' => 'color: {{VALUE}} !important;',
					'.xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'close_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-close' => 'background-color: {{VALUE}} !important;',
					'.xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'close_hover_tab', array( 'label' => esc_html__( 'Hover', 'prdctfltr' ) ) );

		$this->add_control(
			'close_color_hover',
			array(
				'label'     => esc_html__( 'Icon Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-close:hover, {{WRAPPER}} .xwoo-filter-close:focus' => 'color: {{VALUE}} !important;',
					'.xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close:hover, .xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close:focus' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'close_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'prdctfltr' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .xwoo-filter-close:hover, {{WRAPPER}} .xwoo-filter-close:focus' => 'background-color: {{VALUE}} !important;',
					'.xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close:hover, .xwoo-filter-drawer[data-xwoo-owner="xwoo-filter-{{ID}}"] .xwoo-filter-close:focus' => 'background-color: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'drawer_title',
			array(
				'label'       => esc_html__( 'Drawer Title', 'prdctfltr' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Filters', 'prdctfltr' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {

		if ( ! function_exists( 'Prdctfltr' ) || ! class_exists( 'XforWC_Product_Filters_Frontend' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="xwoo-filter-placeholder">' . esc_html__( 'Product Filter plugin is not loaded on the frontend. The widget will render on the live page.', 'prdctfltr' ) . '</div>';
			}
			return;
		}

		// Force-enqueue assets here too; the wp_enqueue_scripts hook can miss
		// edge cases in Elementor's editor preview / AJAX renders.
		wp_enqueue_style( 'xwoo-filter' );
		wp_enqueue_script( 'xwoo-filter' );

		// Inline critical CSS as a safety net: cache busting + Elementor preview
		// re-renders sometimes don't pick up external stylesheets fast enough.
		// These rules MUST land for the popup to behave correctly.
		static $printed_critical_css = false;
		if ( ! $printed_critical_css ) {
			$printed_critical_css = true;
			echo '<style id="xwoo-filter-critical">' .
				'.xwoo-filter-drawer{position:fixed!important;top:0!important;bottom:0!important;z-index:99999;transform:translateX(-100%);visibility:hidden;display:flex!important;flex-direction:column;margin:0!important;}' .
				'.xwoo-filter-drawer.xwoo-mode-popup,.xwoo-mode-popup .xwoo-filter-drawer{top:50%!important;left:50%!important;bottom:auto!important;right:auto!important;width:min(480px,calc(100vw - 32px))!important;max-height:85vh;transform:translate(-50%,-50%) scale(.96);opacity:0;border-radius:12px;overflow:hidden;height:auto!important;}' .
				'.xwoo-filter-drawer.xwoo-mode-popup.xwoo-open,.xwoo-mode-popup .xwoo-filter-drawer.xwoo-open{transform:translate(-50%,-50%) scale(1)!important;opacity:1;visibility:visible;}' .
				'.xwoo-filter-drawer.xwoo-mode-drawer.xwoo-drawer-left{left:0!important;right:auto!important;transform:translateX(-100%);}' .
				'.xwoo-filter-drawer.xwoo-mode-drawer.xwoo-drawer-right{right:0!important;left:auto!important;transform:translateX(100%);}' .
				'.xwoo-filter-drawer.xwoo-mode-fullscreen{inset:0!important;width:100vw!important;max-width:100vw!important;transform:translateY(100%);}' .
				'.xwoo-filter-drawer.xwoo-open{transform:translate(0,0)!important;visibility:visible;}' .
				'.xwoo-mode-popup .xwoo-filter-drawer.xwoo-open{transform:translate(-50%,-50%) scale(1)!important;}' .
				'.xwoo-filter-backdrop{position:fixed!important;inset:0!important;background:rgba(0,0,0,.55);opacity:0;visibility:hidden;z-index:99998;pointer-events:none;}' .
				'.xwoo-filter-backdrop.xwoo-open{opacity:1;visibility:visible;pointer-events:auto;}' .
				'.xwoo-filter-drawer-panel{position:relative!important;background:#fff;width:100%;height:100%;display:flex;flex-direction:column;box-shadow:0 10px 40px rgba(0,0,0,.18);}' .
				'.xwoo-filter-drawer>button.xwoo-filter-close,button.xwoo-filter-close{position:absolute!important;top:12px!important;right:12px!important;left:auto!important;bottom:auto!important;width:32px!important;height:32px!important;min-width:0!important;min-height:0!important;padding:0!important;margin:0!important;border:0!important;background:transparent!important;background-color:transparent!important;background-image:none!important;box-shadow:none!important;color:#af1f31!important;border-radius:50%!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;z-index:10;font-size:0!important;line-height:0!important;cursor:pointer!important;float:none!important;}' .
				'button.xwoo-filter-close svg{width:18px!important;height:18px!important;stroke:currentColor!important;fill:none!important;display:block!important;}' .
				'body.xwoo-filter-locked{overflow:hidden;}' .
			'</style>';
		}

		$settings = $this->get_settings_for_display();

		$display_mode    = isset( $settings['display_mode'] ) ? $settings['display_mode'] : 'inline';
		$collapsible     = isset( $settings['collapsible'] ) && 'yes' === $settings['collapsible'];
		$collapsed       = isset( $settings['collapsed_by_default'] ) && 'yes' === $settings['collapsed_by_default'];
		$single_open     = isset( $settings['single_open'] ) && 'yes' === $settings['single_open'];
		$force_drawer    = isset( $settings['mobile_force_drawer'] ) && 'yes' === $settings['mobile_force_drawer'];
		$drawer_position = isset( $settings['drawer_position'] ) ? $settings['drawer_position'] : 'left';

		$wrap_classes = array(
			'xwoo-filter-wrap',
			'xwoo-mode-' . esc_attr( $display_mode ),
		);
		if ( $collapsible ) {
			$wrap_classes[] = 'xwoo-collapsible';
		}
		if ( $collapsed ) {
			$wrap_classes[] = 'xwoo-collapsed-default';
		}
		if ( $single_open ) {
			$wrap_classes[] = 'xwoo-accordion';
		}
		if ( 'inline' === $display_mode && $force_drawer ) {
			$wrap_classes[] = 'xwoo-mobile-drawer';
		}
		if ( 'drawer' === $display_mode ) {
			$wrap_classes[] = 'xwoo-drawer-' . esc_attr( $drawer_position );
		}

		$instance_id = 'xwoo-filter-' . $this->get_id();

		echo '<div class="' . esc_attr( implode( ' ', $wrap_classes ) ) . '" data-xwoo-id="' . esc_attr( $instance_id ) . '">';

		if ( 'inline' !== $display_mode ) {
			$this->render_trigger( $settings, $instance_id );
		}

		if ( 'drawer' === $display_mode || 'fullscreen' === $display_mode || 'popup' === $display_mode ) {
			echo '<div class="xwoo-filter-backdrop" data-xwoo-close="1" aria-hidden="true"></div>';
			echo '<div class="xwoo-filter-drawer" id="' . esc_attr( $instance_id ) . '" role="dialog" aria-modal="true" aria-hidden="true" tabindex="-1">';

			// Close button as a direct child of the drawer so position:absolute
			// pins it to the drawer's top-right corner regardless of header layout.
			echo '<button type="button" class="xwoo-filter-close" data-xwoo-close="1" aria-label="' . esc_attr__( 'Close filters', 'prdctfltr' ) . '">';
			echo '<svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><path d="M5 5l10 10M15 5L5 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>';
			echo '</button>';

			echo '<div class="xwoo-filter-drawer-panel">';
			$this->render_drawer_header( $settings );
			echo '<div class="xwoo-filter-drawer-body">';
			$this->render_filter( $settings );
			echo '</div>';
			$this->render_drawer_footer();
			echo '</div>';
			echo '</div>';
		} else {
			// Inline. If "force drawer on mobile" is on, we still wrap, JS handles toggling.
			echo '<div class="xwoo-filter-inline" id="' . esc_attr( $instance_id ) . '">';
			$this->render_filter( $settings );
			echo '</div>';

			if ( $force_drawer ) {
				$this->render_trigger( $settings, $instance_id, true );
				echo '<div class="xwoo-filter-backdrop" data-xwoo-close="1" aria-hidden="true"></div>';
			}
		}

		echo '</div>';
	}

	private function render_trigger( $settings, $target_id, $mobile_only = false ) {

		$text          = isset( $settings['trigger_text'] ) && '' !== $settings['trigger_text'] ? $settings['trigger_text'] : esc_html__( 'Filters', 'prdctfltr' );
		$icon_position = isset( $settings['trigger_icon_position'] ) ? $settings['trigger_icon_position'] : 'before';
		$show_count    = isset( $settings['trigger_show_count'] ) && 'yes' === $settings['trigger_show_count'];
		$align_class   = '';

		if ( ! empty( $settings['trigger_align'] ) && 'stretch' === $settings['trigger_align'] ) {
			$align_class = ' xwoo-stretch';
		}

		$wrap_class = 'xwoo-filter-trigger-wrap' . $align_class;
		if ( $mobile_only ) {
			$wrap_class .= ' xwoo-mobile-only';
		}

		echo '<div class="' . esc_attr( $wrap_class ) . '">';
		echo '<button type="button" class="xwoo-filter-trigger" aria-controls="' . esc_attr( $target_id ) . '" aria-expanded="false" data-xwoo-open="' . esc_attr( $target_id ) . '">';

		$icon_html = '';
		if ( ! empty( $settings['trigger_icon']['value'] ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['trigger_icon'], array( 'aria-hidden' => 'true' ) );
			$icon_html = '<span class="xwoo-filter-trigger-icon">' . ob_get_clean() . '</span>';
		}

		if ( 'before' === $icon_position ) {
			echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '<span class="xwoo-filter-trigger-text">' . esc_html( $text ) . '</span>';
		if ( $show_count ) {
			echo '<span class="xwoo-filter-trigger-count" hidden>0</span>';
		}
		if ( 'after' === $icon_position ) {
			echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</button>';
		echo '</div>';
	}

	private function render_drawer_header( $settings ) {
		$title = isset( $settings['drawer_title'] ) && '' !== $settings['drawer_title'] ? $settings['drawer_title'] : esc_html__( 'Filters', 'prdctfltr' );
		echo '<div class="xwoo-filter-drawer-header">';
		echo '<span class="xwoo-filter-drawer-title">' . esc_html( $title ) . '</span>';
		echo '</div>';
		// Close button moved out of header — it's rendered as a direct child of
		// .xwoo-filter-drawer so it always pins to the drawer's top-right corner.
	}

	private function render_drawer_footer() {
		echo '<div class="xwoo-filter-drawer-footer">';
		echo '<button type="button" class="xwoo-filter-reset" data-xwoo-reset="1">' . esc_html__( 'Clear all', 'prdctfltr' ) . '</button>';
		echo '<button type="button" class="xwoo-filter-apply" data-xwoo-close="1">' . esc_html__( 'Show results', 'prdctfltr' ) . '</button>';
		echo '</div>';
	}

	private function render_filter( $settings ) {

		$widget_opt = array(
			'style'             => isset( $settings['style_mode'] ) ? $settings['style_mode'] : 'pf_inherit',
			'preset'            => isset( $settings['preset'] ) ? $settings['preset'] : 'default',
			'disable_overrides' => isset( $settings['disable_overrides'] ) && 'yes' === $settings['disable_overrides'] ? 'yes' : '',
			'id'                => '',
			'class'             => 'xwoo-filter',
		);

		global $prdctfltr_global;
		$prdctfltr_global['widget_search'] = true;
		$prdctfltr_global['unique_id']     = wp_doing_ajax() && isset( $prdctfltr_global['unique_id'] ) ? $prdctfltr_global['unique_id'] : uniqid( 'xwoo-filter-' );

		\XforWC_Product_Filters_Frontend::$settings['widget'] = $widget_opt;
		$prdctfltr_global['widget_options']    = $widget_opt;
		$prdctfltr_global['preset']            = $widget_opt['preset'];
		$prdctfltr_global['disable_overrides'] = $widget_opt['disable_overrides'];

		if ( ! wp_doing_ajax() && ! isset( $prdctfltr_global['done_filters'] ) ) {
			\XforWC_Product_Filters_Frontend::make_global( $_REQUEST, 'FALSE' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		echo '<div class="xwoo-filter">';
		include \XforWC_Product_Filters_Frontend::$dir . 'templates/product-filter.php';
		echo '</div>';

		\XforWC_Product_Filters_Frontend::$settings['widget'] = null;
		$prdctfltr_global['widget_search']  = null;
		$prdctfltr_global['widget_options'] = array();
		unset( $prdctfltr_global['unique_id'] );
		unset( $prdctfltr_global['preset'] );
		unset( $prdctfltr_global['disable_overrides'] );
	}
}
