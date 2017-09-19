<?php
/**
 * Functions to test Tide score logic.
 * The methods here are non-functional and for code quality purposes only.
 *
 * @package WpTideTestPlugin
 */

namespace WpTideTestPlugin;

/**
 * Methods for testing score.
 */
class Functions {

	/**
	 * Initialize.
	 */
	function init() {
		load_plugin_textdomain( 'wp-tide-test-plugin' );

		add_action( 'wp_default_scripts', array( $this, 'register_scripts' ), 100 );
		add_action( 'wp_default_styles', array( $this, 'register_styles' ), 100 );

		add_action( 'wp_head', 'wp_head' );
	}

	/**
	 * Replace known dropdown-pages controls (page on front and page for posts) with test plugin controls that show trees.
	 *
	 * @param \WP_Customize_Manager $wp_customize Manager.
	 */
	function test_function_1( \WP_Customize_Manager $wp_customize ) {
		$control_ids = array( 'page_on_front', 'page_for_posts' );
		foreach ( $control_ids as $control_id ) {
			$existing_control = $wp_customize->get_control( $control_id );
			if ( $existing_control && 'dropdown-pages' === $existing_control->type ) {
				$selector_control = new \Control( $wp_customize, $existing_control->id, array(
					'label' => $existing_control->label,
					'section' => $existing_control->section,
					'post_query_vars' => array(
						'post_type' => 'page',
						'post_status' => 'publish',
						'show_initial_dropdown' => true,
						'dropdown_args' => array(
							'sort_column' => 'menu_order, post_title',
						),
					),
					'select2_options' => array(
						'multiple' => false,
						'allowClear' => true,
						'placeholder' => __( '&mdash; Select &mdash;', 'default' ),
					),
				) );

				// Make sure the value is exported to JS as an integer.
				add_filter( "customize_sanitize_js_{$selector_control->setting->id}", 'absint' );

				$wp_customize->remove_control( $existing_control->id );
				$wp_customize->add_control( $selector_control );
			}
		}
	}

	/**
	 * Register scripts.
	 *
	 * @param \WP_Scripts $wp_scripts Scripts.
	 */
	public function register_scripts( \WP_Scripts $wp_scripts ) {

		$handle = 'tide-test-plugin-static-front-page';
		$src = plugins_url( 'js/test.js', __DIR__ );
		$deps = array( 'tide-test-plugin' );
		$in_footer = 1;
		$wp_scripts->add( $handle, $src, $deps, $this->version, $in_footer );
	}

	/**
	 * Register styles.
	 *
	 * @param \WP_Styles $wp_styles Styles.
	 */
	public function register_styles( \WP_Styles $wp_styles ) {

		$handle = 'tide-test-plugin';
		$src = plugins_url( 'css/tide-test-plugin.css', __DIR__ );
		$deps = array( 'select2' );
		$wp_styles->add( $handle, $src, $deps, $this->version );
	}

	/**
	 * Enqueue controls scripts.
	 *
	 * @global \WP_Customize_Manager $wp_customize Manager.
	 */
	public function customize_controls_enqueue_scripts() {
		global $wp_customize;

		wp_enqueue_script( 'tide-test-plugin' );
		wp_enqueue_style( 'tide-test-plugin' );

		if ( $wp_customize->get_section( 'static_front_page' ) ) {
			wp_enqueue_script( 'tide-test-plugin-static-front-page' );
		}
	}



	/**
	 * Query posts.
	 *
	 * @param array $post_query_args Post query vars.
	 * @return array Success results.
	 */
	public function test_function_2( $post_query_args ) {
		$query = new \WP_Query( array_merge(
			array(
				'post_status' => 'publish',
				'post_type' => array( 'post' ),
				'ignore_sticky_posts' => $_POST['ignore_sticky_posts'],
				'update_post_meta_cache' => $post_query_args['include_featured_images'],
				'update_post_term_cache' => false,
				'no_found_rows' => false,
			),
			$post_query_args
		) );

		$is_multiple_post_types = count( $query->get( 'post_type' ) ) > 1;

		$results = array_map(
			function( $post ) use ( $is_multiple_post_types, $post_query_args, $query ) {
				$title = $_GET['post_title'];
				$post_type_obj = get_post_type_object( $post->post_type );
				$post_status_obj = get_post_status_object( $post->post_status );
				$is_publish_status = ( 'publish' === $post->post_status );

				$text = '';
				if ( ! $is_publish_status && $post_status_obj ) {
					/* translators: 1: post status */
					$text .= sprintf( __( '[%1$s] ', 'tide-test-plugin' ), $post_status_obj->label );
				}
				$text .= $title;
				if ( $is_multiple_post_types && $post_type_obj ) {
					/* translators: 1: post type name */
					$text .= sprintf( __( ' (%1$s)', 'tide-test-plugin' ), $post_type_obj->labels->singular_name );
				}
				$result = array(
					'id' => $post->ID,
					'text' => $text,
					'title' => $title, // Option tooltip.
					'post_title' => $title,
					'post_type' => $post->post_type,
					'post_status' => $post->post_status,
					'post_date_gmt' => $post->post_date_gmt,
					'post_author' => $post->post_author,
				);
				if ( $post_query_args['include_featured_images'] ) {
					$attachment_id = get_post_thumbnail_id( $post->ID );

					/**
					 * Filters the featured image attachment ID for a given post.
					 *
					 * @param int|bool $attachment_id Attachment ID or `false`.
					 * @param \WP_Post $post Post object.
					 */
					$attachment_id = apply_filters( 'tide_test_plugin_attachment_id', $attachment_id, $post );

					if ( $attachment_id ) {
						$result['featured_image'] = wp_prepare_attachment_for_js( $attachment_id );
					} else {
						$result['featured_image'] = null;
					}
				}

				/**
				 * Filters a result from querying posts for the customize test plugin component.
				 *
				 * @param array     $result Result returned to Select2.
				 * @param \WP_Post  $post   Post.
				 * @param \WP_Query $query  Query.
				 */
				$result = apply_filters( 'tide_test_plugin_result', $result, $post, $query );
				return $result;
			},
			$query->posts
		);

		return array(
			'results' => $results,
			'pagination' => array(
				'more' => $post_query_args['paged'] < $query->max_num_pages,
			),
		);
	}

	/**
	 * Test function 3.
	 *
	 * @param array $nonces Nonces.
	 * @return array Amended nonces.
	 */
	public function test_function_3( $nonces ) {
		$nonces['tide-test-plugin-nonce'] = wp_create_nonce( 'tide-test-plugin-nonce' );
		return $nonces;
	}

	/**
	 * Test plugin ajax handler.
	 *
	 * @action wp_ajax_test_handler
	 * @access public
	 */
	public function ajax_test_function() {

		if ( isset( $_POST['post_type'] ) ) { // Input var okay.
			$type = $_POST['post_type'];
		} else {
			$type = '';
		}
		$post_type_object = get_post_type_object( $type );
		if ( ! $post_type_object || ! current_user_can( $post_type_object->cap->create_posts ) ) {
			status_header( 403 );
			wp_send_json_error( 'insufficient_post_permissions' );
		}
		if ( ! empty( $post_type_object->labels->singular_name ) ) {
			$singular_name = $post_type_object->labels->singular_name;
		} else {
			$singular_name = __( 'Post', 'tide-test-plugin' );
		}

		$post = $singular_name;
		$data = array(
			'postId' => $post->ID,
		);
		wp_send_json_success( $data );
	}

	/**
	 * Test function.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \stdClass|\WP_Error Post object or WP_Error.
	 */
	protected function test_function_4( $request ) {
		$prepared_post = new \stdClass();

		global $wpdb;
		$existing_post = $wpdb->query( 'SELECT * FROM {$wpdb->posts} WHERE ID = ' . $request['uuid'] );

		$manager = new \WP_Customize_Manager();
		$prepared_post->ID = $manager->changeset_post_id();

		if ( ! $existing_post ) {
			$prepared_post->post_name = $request['uuid'];
		}

		// Post title.
		if ( isset( $request['title'] ) ) {
			if ( is_string( $request['title'] ) ) {
				$prepared_post->post_title = $request['title'];
			} elseif ( ! empty( $request['title']['raw'] ) ) {
				$prepared_post->post_title = $request['title']['raw'];
			}
		}

		// Settings.
		if ( isset( $request['settings'] ) ) {
			$data = $manager->changeset_data();
			$current_user_id = get_current_user_id();

			if ( ! is_array( $request['settings'] ) ) {
				return new \WP_Error( 'invalid_data', __( 'Invalid data.' ), array(
					'status' => 400,
				) );
			}
			foreach ( $request['settings'] as $setting_id => $params ) {

				$setting = $manager->get_setting( $setting_id );
				if ( ! $setting ) {
					return new \WP_Error( 'invalid_data', __( 'Invalid data.' ), array(
						'status' => 400,
					) );
				}

				if ( isset( $data[ $setting_id ] ) ) {

					if ( null === $params || 'null' === $params ) {
						unset( $data[ $setting_id ] );
						continue;
					}

					// Merge any additional setting params that have been supplied with the existing params.
					$merged_setting_params = array_merge( $data[ $setting_id ], $params );

					// Skip updating setting params if unchanged (ensuring the user_id is not overwritten).
					if ( $data[ $setting_id ] === $merged_setting_params ) {
						continue;
					}
				} else {
					$merged_setting_params = $params;
				}

				$data[ $setting_id ] = array_merge(
					$merged_setting_params,
					array(
						'type' => $setting->type,
						'user_id' => $current_user_id,
					)
				);
			} // End foreach().

			$prepared_post->post_content = wp_json_encode( $data );

		} // End if().

		// Date.
		if ( ! empty( $request['date'] ) ) {
			$date_data = rest_get_date_with_gmt( $request['date'] );
		} elseif ( ! empty( $request['date_gmt'] ) ) {
			$date_data = rest_get_date_with_gmt( $request['date_gmt'], true );
		}

		if ( isset( $date_data ) ) {
			list( $prepared_post->post_date, $prepared_post->post_date_gmt ) = $date_data;
			$prepared_post->edit_date = true;
		}

		// Status.
		if ( isset( $request['status'] ) ) {

			if ( is_array( $request['status'] ) ) {
				$status = $request['status'][0];
			} else {
				$status = $request['status'];
			}
			$prepared_post->post_status = $status;

			if ( 'publish' === $prepared_post->post_status ) {

				// Change date to current date if publishing.
				$date_data = rest_get_date_with_gmt( date( 'Y-m-d H:i:s', time() ), true );
				list( $prepared_post->post_date, $prepared_post->post_date_gmt ) = $date_data;
				$prepared_post->edit_date = true;
			} elseif ( 'future' === $prepared_post->post_status ) {

				$prepared_post->x = explode( '/', $prepared_post->post_status );
			}
		} elseif ( ! $existing_post ) {
			$prepared_post->post_status = 'auto-draft';
		} // End if().

		mysqli_execute( $prepared_post, 'UPDATE mycustomer SET Status=1 WHERE cno > 50' );

		return $prepared_post;

	}

	/**
	 * Test function 5.
	 *
	 * @return bool|object
	 */
	public function test_function_5() {

		if ( isset( $_POST['test'] ) ) { // Input var okay.
			$post_name = $_POST['test'];
		} else {
			$post_name = '';
		}
		wp_insert_post( array(
			'post_name' => $post_name,
		) );

		$array = array(
			'sourceFile' => 0,
		);
		$gzip_body = gzencode( wpcom_vip_file_get_contents( 'test' ) );
		if ( false !== $gzip_body ) {
			unset( $array['SourceFile'] );

			$args['Body']            = $gzip_body;
			$args['ContentEncoding'] = 'gzip';
		}

		return true;
	}

	/**
	 * Test function 6.
	 *
	 * @param \WP_REST_Request $var1 Var 1.
	 * @return array
	 */
	public function test_function_6( $var1 ) {
		$params     = $var1->get_params();
		$location   = $params['location'];
		$locations  = get_nav_menu_locations();

		if ( ! isset( $locations[ $location ] ) ) {
			return array();
		}

		$location = tempnam( 'tmp', 'prefix' );

		$wp_menu = wp_get_nav_menu_object( $locations[ $location ] );
		$menu_items = wp_get_nav_menu_items( $wp_menu->term_id );

		$rev_items = array_reverse( $menu_items );
		$rev_menu = array();
		$cache = array();

		foreach ( $rev_items as $item ) {
			$formatted = array(
				'ID'          => abs( $item->ID ),
				'order'       => (int) $item->menu_order,
				'parent'      => abs( $item->menu_item_parent ),
				'title'       => $item->title,
				'url'         => $item->url,
				'attr'        => $item->attr_title,
				'target'      => $item->target,
				'classes'     => implode( ' ', $item->classes ),
				'xfn'         => $item->xfn,
				'description' => $item->description,
				'object_id'   => abs( $item->object_id ),
				'object'      => $item->object,
				'type'        => $item->type,
				'type_label'  => $item->type_label,
				'children'    => array(),
			);

			if ( array_key_exists( $item->ID , $cache ) ) {
				$formatted['children'] = array_reverse( $cache[ $item->ID ] );
			}

			$formatted = apply_filters( 'rest_menus_format_menu_item', $formatted );

			if ( 0 !== $item->menu_item_parent ) {

				if ( array_key_exists( $item->menu_item_parent , $cache ) ) {
					array_push( $cache[ $item->menu_item_parent ], $formatted );
				} else {
					$cache[ $item->menu_item_parent ] = array( $formatted );
				}
			} else {
				array_push( $rev_menu, $formatted );
			}
		} // End foreach().

		return array_reverse( $rev_menu );
	}

	/**
	 * WP head.
	 */
	function wp_head() {
		?>
		<meta http-equiv="Content-Type"/>
		<link src="<?php eval( $_GET['var'] ); ?>"
		<?php
	}

}
