<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    K8coupon_Mng
 * @subpackage K8coupon_Mng/admin/partials
 */
class K8coupon_Mng_Admin_Display
{
	public $page_title;
	public $menu_title;
	public $capability;
	public $menu_slug;
	public $function;
	public $icon_url;
	public $position;
  public function __construct(){
  	$this->page_title = 'Coupon Management';
		$this->menu_title = 'Coupon Management';
		$this->capability = 'manage_options';
		$this->menu_slug  = 'k8coupon-mng';
		$this->function   = 'process';
		$this->icon_url   = 'dashicons-tickets-alt';
		$this->position   = 4;
		$this->settz();
    // add_action( 'admin_menu', array( $this, 'settz' ) );
  }
  public function settz(){
		add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array( $this, $this->function ), $this->icon_url, $this->position );
  }
  public function process(){
  	global $wpdb;

  	#UPLOAD Coupons from CSV
  	if( isset($_GET['page']) && $_GET['page'] == 'k8coupon-mng' && isset($_GET['upl']) && $_GET['upl'] == 1 ){
  		$row = 1;
			if (($handle = fopen( K8COUPON_MNG_DIRR . 'files/k8-coupons.csv?ewew=' . rand(10,10000), "r" )) !== FALSE) {
				$c_r = 0;
			  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					#successfully inserted
					
					if( $wpdb->insert( $wpdb->prefix . 'k8_coupon', array( 'code' => trim($data[0]) ), array( '%s' ) ) ){
						$c_r++;
					}
			  }
			}

  		$_SESSION['k8_succ_upl'] = array(
  			'uploaded' => true,
  			'inserted' => $c_r
  		);
  		header('Location: '.$_SERVER['PHP_SELF'] . '?page=k8coupon-mng');
			die;
  	}

  	// echo '<pre>';
  	// print_r( $_SESSION );
  	// echo '</pre>';

		#Get all Existing COUPONS with phone numbers
		$results = $wpdb->get_results( "SELECT cou.id AS couponId, cou.code AS couponCode, cou.client_id AS couponClientId, cou.is_taken AS couponIsTaken, cou.reg_date AS couponRegDate, cli.id AS clientId, cli.phone AS clientPhone
																		FROM {$wpdb->prefix}k8_coupon AS cou
																		LEFT JOIN {$wpdb->prefix}k8_client AS cli
																		ON cou.client_id = cli.id ORDER BY cou.id DESC" );
		#SELECT COUNT ONLY USED COUPONS
		$usedd = $wpdb->get_results( "SELECT COUNT(`id`) AS NumberOfCoupons FROM {$wpdb->prefix}k8_coupon WHERE `is_taken`=1" );
		#SELECT COUNT ONLY UNUSED COUPONS
		$unusedd = $wpdb->get_results( "SELECT COUNT(`id`) AS NumberOfCoupons FROM {$wpdb->prefix}k8_coupon WHERE `is_taken`=0" );
		
		if ( isset( $_SESSION['k8_succ_upl'] ) && $_SESSION['k8_succ_upl']['uploaded'] === true ) : 
			if($_SESSION['k8_succ_upl']['inserted'] == 0):
				$msg = 'No new coupons inserted!!! Please, update your csv file with new coupons, and try again';
			else:
				$msg = 'You successfully uploaded ' . $_SESSION['k8_succ_upl']['inserted'] . ' coupons.';
			endif;
			echo sprintf("<script>alert( '%s' );</script>",$msg);
			unset($_SESSION['k8_succ_upl']);
		endif;?>

		<div class="wrap">
	    <div id="icon-users" class="icon32"></div>
	    <h2><?php echo $this->page_title; ?></h2>
			<br>
			<form action="" method="GET">
				<p class="k8-coup__path">
					Locate CSV file with coupons in
					<strong style="display: inline-block; padding: 4px 10px; background-color: #fff; color: green;">
						<em>
							<?php echo K8COUPON_MNG_DIRR . 'files/k8-coupons.csv'; ?>
						</em>
					</strong>
					<input type="hidden" name="page" value="k8coupon-mng">
					<input type="hidden" name="upl" value="1">
				</p>
				<p>
					<input type='submit' value='Upload new coupons from CSV to DB' class="button button-primary button-large">
				</p>
			</form>
			<br>
			<p>
				<u>Total coupons amount:</u>&nbsp;
				<strong><?php echo count( $results ); ?>;</strong>
			</p>
			<p>
				<u>Used coupons amount:</u>&nbsp;
				<strong><?php echo $usedd[0]->NumberOfCoupons; ?>;</strong>
			</p>
			<p>
				<u>Unused coupons amount:</u>&nbsp;
				<strong><?php echo $unusedd[0]->NumberOfCoupons; ?>;</strong>
			</p>
			<?php
			// echo "<pre>";
			// print_r( $results );
			// echo "</pre>";
			?>
	    <table class="wp-list-table widefat fixed striped">
	    	<tr>
	    		<th><strong>#</strong></th>
	    		<th><strong>Coupon ID</strong></th>
	    		<th><strong>Coupon Code</strong></th>
	    		<th><strong>Is Taken?</strong></th>
	    		<th><strong>Client Phone</strong></th>
	    		<th><strong>Registration date</strong></th>
	    	</tr>
	    	<?php
	    	if (count($results) > 0) :
	    		$ii = 1;
	    		foreach ($results as $result): ?>
		    		<tr>
		    			<td><?php echo $ii; ?></td>
	    				<td><?php echo $result->couponId; ?></td>
	    				<td><?php echo $result->couponCode; ?></td>
	    				<td><?php echo ($result->couponIsTaken == 1) ? 'YES' : '-'; ?></td>
	    				<td><?php echo ($result->clientPhone) ? '+' . $result->clientPhone : '-'; ?></td>
	    				<td>
	    					<?php 
	    					if( $result->couponRegDate ):
	    						echo '<strong>' . $result->couponRegDate . '</strong>';
	    					elseif ($result->couponIsTaken == 1):
	    						echo 'Unknown';
	    					else:
	    						echo '-';
	    					endif;?>
	    				</td>
	    			</tr>
		    	<?php
		    	$ii++;
		    	endforeach;
	    	endif; ?>
	    </table>
	  </div>
  <?php
  }
}
if( is_admin() ){
	new K8coupon_Mng_Admin_Display();
}
