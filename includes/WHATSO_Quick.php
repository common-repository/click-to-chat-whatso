<?php
 

class WHATSO_Quick {
	
	
	
	/**
	 * Initialize constructor.
	 */
	public function __construct () {
		add_action( 'init', array( $this, 'accountsPostType1' ) );
		if ( ! is_admin() ) {
			return;
		}
		
		add_filter( 'manage_whatso_accounts_posts_columns1', array( $this, 'accountTabulationHeader1' ) );
		add_action( 'manage_whatso_accounts_posts_custom_column1', array( $this, 'accountTabulationData1' ), 10, 2 );
	 	add_filter( 'manage_edit-whatso_accounts_sortable_columns', array( $this, 'accountTabulationSorting' ) );
		add_action( 'add_meta_boxes', array( $this, 'addMetaBoxes1' ) );
		add_action( 'save_post', array( $this, 'saveMetaBoxes' ) );	
			
		
		
		
			
			
	}
	
	/**
	 * Create menu in wp-admin

	 */
	
	
	
	
	public function accountsPostType1 () {
		$labels = array(
			''               => _x( 'Whatso Quick Accounts', 'post type general name', 'whatso' ),
			'singular_name'      => _x( 'Whatso  Quick Account', 'post type singular name', 'whatso' ),
			'menu_name'          => _x( 'Quick Accounts', 'admin menu', 'whatso' ),
			'name_admin_bar'     => _x( 'Account', 'add new on admin bar', 'whatso' ),
			'add_new'            => _x( 'Add New', 'book', 'whatso' ),
			'add_new_item'       => __( 'Add New WhatsApp Account', 'whatso' ),
			'new_item'           => __( 'New Account', 'whatso' ),
			'edit_item'          => __( 'Edit Account', 'whatso' ),
			
			'view_item'          => __( 'View Account', 'whatso' ),
			'all_items'          => __( 'Quick Setup', 'whatso' ),
			'search_items'       => __( 'Search Accounts', 'whatso' ),
			'parent_item_colon'  => __( 'Parent Accounts:', 'whatso' ),
			'not_fonameund'          => __( 'No accounts found.', 'whatso' ),
			'not_found_in_trash' => __( 'No accounts found in Trash.', 'whatso' )
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Whatso Quick Accounts', 'whatso' ),
			'public'              => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'whatso_parent',
			'query_var'           => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'menu_position'       => null,
			'supports'            => array( 'title', 'thumbnail' )
		);
	
		register_post_type( 'whatso_quick', $args );


	/**
	 * Function for change title
	 */
	

	
	/** message box**/
	
	}
	
	
	public function addMetaBoxes1 () {
		
		add_meta_box(
			'whatso-account-information',
			esc_html__( 'Whatso Account Information', 'whatso' ),
			array( $this, 'accountInformation1' ),
			array( 'whatso_quick' ),
			'normal'
		);
	

		
	}
	public function accountInformation1 ( $post ) {
		
		//print_r($post);die;
			
		global $pagenow;
		
		$new = 'post-new.php' === $pagenow ? true : false;
		
		$number = sanitize_text_field( get_post_meta( $post->ID, 'whatso_number', true ) );
		$name = sanitize_text_field( get_post_meta( $post->ID, 'whatso_name', true ) );
		
		?>
		
		<table class="form-table" id="whatso-custom-wc-button-settings">
		<caption> Whatso Create Account</caption>
			<tbody>
				<tr>
					<th scope="row"><label for="whatso_number"><?php esc_html_e( 'Mobile Number with country code', 'whatso' ); ?></label></th>
					<td>
						<p>
							<input type="text" class="widefat" id="whatso_number" name="whatso_number" placeholder="Mobile Number with country code Eg. +919876543210" value="<?php echo esc_attr( $number ); ?>" />
							
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="whatso_name"><?php esc_html_e( 'Name', 'whatso' ); ?></label></th>
					<td>
						<input type="text" id="whatso_name" name="whatso_name" value="<?php echo esc_attr( $name ); ?>" placeholder="Type name of the person E.g. James Anderson" class="widefat" />
					</td>
				</tr>
				
			</tbody>
		</table>
		
		<?php
		
		wp_nonce_field( 'whatso_account_meta_box', 'whatso_account_meta_box_nonce' );
	}

	public function accountTabulationSorting ( $columns ) {
		$columns['number'] = 'number';
		$columns['time'] = 'time';
		return $columns;
		
	}
	public function saveMetaBoxes ( $post_id ) {
			
		
		
		/* Check if our nonce is set. */
		if ( ! isset( $_POST['whatso_account_meta_box_nonce'] ) ) {
			return;
		}
		
		$nonce = sanitize_text_field( wp_unslash( $_POST['whatso_account_meta_box_nonce'] ) );
		
		/* Verify that the nonce is valid. */
		if ( ! wp_verify_nonce( $nonce, 'whatso_account_meta_box' ) ) {
			return;
		}
		
		/* WhatsApp Account Information */
		
		
		$number = isset( $_POST['whatso_number'] ) ? sanitize_text_field( wp_unslash( $_POST['whatso_number'] ) ) : '';
		
		$name = isset( $_POST['whatso_name'] ) ? sanitize_text_field( wp_unslash( $_POST['whatso_name'] ) ) : '';
		$title = isset( $_POST['whatso_title'] ) ? sanitize_text_field( wp_unslash( $_POST['whatso_title'] ) ) : '';
		
       
		
		update_post_meta( $post_id, 'whatso_number', $number );
		update_post_meta( $post_id, 'whatso_name', $name );
		update_post_meta( $post_id, 'whatso_title', $title );
		
		
		
		
	}
	
}	
	

?>