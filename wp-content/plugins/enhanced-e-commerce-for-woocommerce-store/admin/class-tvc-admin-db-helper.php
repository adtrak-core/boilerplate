<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'TVC_Admin_DB_Helper' ) ) {
	Class TVC_Admin_DB_Helper{
		public function __construct() {
			$this->includes();
		}
		public function includes() {
	  		//require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );    
		}

		public function tvc_row_count($table, $field_name = "*"){
			if($table ==""){
				return;
			}else{
				global $wpdb;
				$tablename = $wpdb->prefix .$table;
				$sql = "select count(".$field_name.") from ".$tablename;
				return $wpdb->get_var($sql);
			}			
		}

		public function tvc_add_row($table, $t_data){
			if($table =="" || $t_data == ""){
				return;
			}else{
				global $wpdb;
				$tablename = $wpdb->prefix .$table;
				return $wpdb->insert($tablename, $t_data);
			}
		}

		public function tvc_update_row($table, $t_data, $where){
			if($table =="" || $t_data == "" ||  $where == ""){
				return;
			}else{
				global $wpdb;
				$tablename = $wpdb->prefix .$table;
				return $wpdb->update($tablename, $t_data, $where);
			}
		}

		public function tvc_check_row_with_num_where($table, $where, $field_name = "*"){
			if($table =="" ||  $where == ""){
				return;
			}else if(is_array($where)){
				global $wpdb;
				$tablename = $wpdb->prefix .$table;
				$key = "";
				foreach ($where as $key => $value) {
					$key=($key!="")?$key."=%d":", ".$key."=%d";
				}
				$sql =  $wpdb->prepare("select count(".$field_name.") from ".$tablename. "where ".$key,$where);
				return $wpdb->get_var($sql);
			}
			return ;
		}

		public function tvc_check_row($table, $where){
			global $wpdb;
			$tablename = $wpdb->prefix .$table;
			if($table =="" ||  $where == ""){
				return;
			}else{
				$sql = "select count(*) from ".$tablename." where ".$where;
				return $wpdb->get_var($sql);
			}
		}

		public function tvc_get_results_in_array($table, $where, $fields, $concat = false){
			global $wpdb;
			$tablename = $wpdb->prefix .$table;
			if($table =="" ||  $where == "" || $fields == ""){
				return;
			}else{				
				if($concat == true){
					$fields = implode(',\'_\',', $fields);
					$sql = "select CONCAT(".$fields.") as p_c_id from ".$tablename." where ".$where;
					return $wpdb->get_col($sql);
				}else{
					$fields = implode(',', $fields);
					$sql = "select ".$fields." from ".$tablename." where ".$where;
					return $wpdb->get_results($sql, ARRAY_A);
				}				
			}
		}

		public function tvc_get_results($table, $where = null, $fields = array()){
			global $wpdb;
			$tablename = $wpdb->prefix .$table;
			if($table =="" ){
				return;
			}else {
				$p_where ="";
				if($where){
					$p_where = "where";
				}
				if( !empty($fields) )	{			
					$fields = implode(',', $fields);
					$sql = "select ".$fields." from ".$tablename." ".$p_where." ".$where;
					return $wpdb->get_results($sql);
				}else{
					$sql = "select * from ".$tablename." ".$p_where." ".$where;
					return $wpdb->get_results($sql);
				}							
			}
		}

		public function tvc_get_last_row($table, $fields=null){
			if($table ==""){
				return;
			}else{
				global $wpdb;
				$tablename = $wpdb->prefix .$table;
				$sql = "select * from ".$tablename." ORDER BY id DESC LIMIT 1";
				if($fields){
					$fields = implode(',', $fields);
					$sql = "select ".$fields." from ".$tablename." ORDER BY id DESC LIMIT 1";
				}
				
				return $wpdb->get_row($sql,ARRAY_A);
			}
		}

		public function tvc_get_counts_groupby($table, $fields_by){
			global $wpdb;
			$tablename = $wpdb->prefix .$table;
			if($table =="" ||  $fields_by == ""){
				return;
			}else{
				$sql = "select ".$fields_by.", count(*) as count from ".$tablename." GROUP BY ".$fields_by." ORDER BY count DESC ";
				return $wpdb->get_results($sql, ARRAY_A);
			}
		}	

		public function tvc_safe_truncate_table($table){
			global $wpdb;
			$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );   
      if ( $wpdb->get_var( $query ) === $table ) {
      	$wpdb->query("TRUNCATE TABLE IF EXISTS ".$table);
      }
		}
	}
}