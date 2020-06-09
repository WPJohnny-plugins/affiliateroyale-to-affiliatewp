<?php
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
global $wpdb;

// Step 1: Migrate Affiliates data
$results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'users WHERE ID IN (SELECT user_id FROM ' . $wpdb->prefix . 'usermeta WHERE meta_key="wafp_is_affiliate" AND meta_value=1)');
foreach ($results as $r) {
    $user_id = $r->ID;
    // $rate      = $wpdb->get_var( $wpdb->prepare( "SELECT attr_value FROM {$wpdb->prefix}aff_affiliates_attributes WHERE affiliate_id = %d AND attr_key = 'referral.rate'", $affiliate->affiliate_id ) );
    $earnings  = $wpdb->get_var( $wpdb->prepare( "SELECT sum(commission_amount) FROM {$wpdb->prefix}wafp_commissions WHERE affiliate_id = %d", $user_id ) );
    $referrals = $wpdb->get_var( $wpdb->prepare( "SELECT count(affiliate_id) FROM {$wpdb->prefix}wafp_commissions WHERE affiliate_id = %d", $user_id ) );
    $visits    = $wpdb->get_var( $wpdb->prepare( "SELECT count(affiliate_id) FROM {$wpdb->prefix}wafp_clicks WHERE affiliate_id = %d", $user_id ) );

    $data      = array(
        'status'          => 'active',
        'date_registered' => $r->user_registered,
        'user_id'         => $user_id,
        'payment_email'      => $r->user_email,
        'unpaid_earnings'        => ! empty( $earnings ) ? $earnings : '0',
        'referrals'       => ! empty( $referrals ) ? $referrals : '0',
        'visits'          => ! empty( $visits ) ? $visits : '0',
    );

    $wpdb->insert($wpdb->prefix . 'affiliate_wp_affiliates', $data);
}
echo 'Affiliates list are successfully migrated. <br />';

// Step 2: Migrate Visits Data
$affiliate_ids = array();
$results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'affiliate_wp_affiliates');
foreach ($results as $r) {
    $affiliate_ids[$r->user_id] = $r->affiliate_id;
}

$results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'wafp_clicks');
foreach ($results as $r) {
    $data      = array(
        'affiliate_id'          => $affiliate_ids[$r->affiliate_id],
        'referral_id' => '',
        'url'      => 'https://yoursite.com',
        'referrer'        => $r->referrer,
        'ip'       => $r->ip,
        'date'          => $r->created_at,
    );

    $wpdb->insert($wpdb->prefix . 'affiliate_wp_visits', $data);
}
echo 'Visits data are successfully migrated. <br />';

// Step 3: Migrate Referrals Data
$affiliate_ids = array();
$results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'affiliate_wp_affiliates');
foreach ($results as $r) {
    $affiliate_ids[$r->user_id] = $r->affiliate_id;
}

$results = $wpdb->get_results('SELECT wwt.* FROM ' . $wpdb->prefix . 'wafp_commissions AS wwc, ' . $wpdb->prefix . 'wafp_transactions AS wwt WHERE wwc.transaction_id = wwt.id');
foreach ($results as $r) {
    $find_user_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'users WHERE user_email="' . $r->cust_email . '"');
    $user_id = count( $find_user_results ) ? $find_user_results[0]->ID : '0';
    $first_name = get_usermeta($user_id, 'first_name', true); if ($first_name == NULL) { $first_name = ''; }
    $last_name = get_usermeta($user_id, 'last_name', true); if ($last_name == NULL) { $last_name = ''; }
    
    $user = array(
        'user_id' => $user_id,
        'email' => $r->cust_email,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'ip' => $r->ip_addr,
        'date_created' => $r->created_at
    );
    if ($wpdb->insert($wpdb->prefix . 'affiliate_wp_customers', $user)) {
        $customer_id = $wpdb->insert_id;
    }
    
    $customer_meta = array(
        'affwp_customer_id' => $customer_id,
        'meta_key' => 'affiliate_id',
        'meta_value' => $affiliate_ids[$r->affiliate_id]
    );
    $wpdb->insert($wpdb->prefix . 'affiliate_wp_customermeta', $customer_meta);
        
    $visit_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'affiliate_wp_visits WHERE affiliate_id=' . $affiliate_ids[$r->affiliate_id] . ' AND ip="' . $r->ip_addr . '"');
    if (count($visit_results) > 0) {
        $visit_id = $visit_results[0]->visit_id;
    } else {
        $visit_id = 0;
    }
    $subscription_results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'mepr_subscriptions WHERE user_id=' . $user_id);
    if (count($subscription_results) > 0) {
        $custom = serialize(array('subscription_id' => $subscription_results[0]->id, 'is_coupon_referral' => 0));
    } else {
        $custom = '';
    }
    
    $data      = array(
        'affiliate_id'          => $affiliate_ids[$r->affiliate_id],
        'visit_id' => $visit_id,
        'customer_id'      => $customer_id,
        'description'        => $r->item_name,
        'status'       => 'paid',
        'amount'          => $r->commission_amount,
        'currency'          => 'USD',
        'custom' => $custom,
        'context' => 'memberpress',
        'type' => 'sale',
        'reference' => $r->trans_num,
        'date' => $r->created_at,
    );

    if ($wpdb->insert($wpdb->prefix . 'affiliate_wp_referrals', $data)) {
        $referral_id = $wpdb->insert_id;
    }
    
    $visit_data = array('referral_id' => $referral_id);
    $where = array('visit_id' => $visit_results[0]->visit_id);
    $wpdb->update($wpdb->prefix . 'affiliate_wp_visits', $visit_data, $where);
}
echo 'Referrals data are successfully migrated. And Customers data were created. Visit data table were updated with referral ids.<br />';
exit;


?>
