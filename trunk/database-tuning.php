<?php
/*
	Database Tuning

	Adds some indexes and allows analyzing and optimizing tables


	Plugin Name: Database Tuning
	Plugin URI: http://rabich.de/database-tuning/
	Description: Mit dem Plugin werden Wartungsarbeiten an den Datenbanktabellen möglich. Zusätzlich werden Indizes zur Performancesteigerung angelegt.
	Version: 0.6e
	Author: Dietmar Rabich
	Author URI: http://rabich.de/


	Copyright (c) 2007 Dietmar Rabich, Duelmen, Germany

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License (version 2) as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/



// ============================================================
// here i am
// ============================================================
define('DATABASETUNING', true);



// ============================================================
// Localization
// ============================================================

$dbt_domain	= 'database-tuning';
$dbt_is_setup	= false;

// setup
function dbt_setup() {
	global $dbt_domain, $dbt_is_setup;

	if ( $dbt_is_setup ) {
		return;
	}

	load_plugin_textdomain($dbt_domain, 'wp-content/plugins');

	$dbt_is_setup	= true;
}

// output
function dbt__($s) {
	global $dbt_domain;

	dbt_setup();

	return __($s, $dbt_domain);
}

function dbt_e($s) {
	global $dbt_domain;

	dbt_setup();

	_e($s, $dbt_domain);
}



// ============================================================
// globals
// ============================================================

define('DBT_ONEDAY', 86400);
define('DBT_WPINDEXES', '2.5');
define('DBT_OPTION_OPTIMIZE', 'dbt_auto_optimize');
define('DBT_OPTION_DELETE_SPAM', 'dbt_auto_deletespam');
define('DBT_OPTION_REMOVE_IP', 'dbt_auto_removeip');
define('DBT_OPTION_ACTIVE', 'dbt_active');
define('DBT_OPTION_OPTIMIZE_DEF', 7);
define('DBT_OPTION_DELETE_SPAM_DEF', 14);
define('DBT_OPTION_REMOVE_IP_DEF', 21);
define('DBT_OPTION_ACTIVE_DEF', false);


// definition indexes
$dbt_indexes = array(
	array(
		tabname => $wpdb->posts,
		indexes => array(
			array(indname => 'dbt_ix1', columns => 'post_date_gmt, post_status, post_author'),
			array(indname => 'dbt_ix2', columns => 'post_date, post_status'),
			array(indname => 'dbt_ix3', columns => 'post_author')
		)
	)
);


// WordPress 2.5 (and older) indexes (but no primary and unique keys)
$dbt_wp_indexes = array(
	array(
		tabname	=> $wpdb->term_relationships,
		indexes	=> array(
			array(indname => 'term_taxonomy_id', columns => 'term_taxonomy_id')
		)
	),
	array(
		tabname => $wpdb->categories,
		indexes => array(
			array(indname => 'category_nicename', columns => 'category_nicename')
		)
	),
	array(
		tabname => $wpdb->comments,
		indexes => array(
			array(indname => 'comment_approved', columns => 'comment_approved'),
			array(indname => 'comment_post_ID', columns => 'comment_post_ID'),
			array(indname => 'comment_approved_date_gmt', columns => 'comment_approved, comment_date_gmt'),
			array(indname => 'comment_date_gmt', columns => 'comment_date_gmt')
		)
	),
	array(
		tabname => $wpdb->link2cat,
		indexes => array(
			array(indname => 'link_id', columns => 'link_id, category_id')
		)
	),
	array(
		tabname => $wpdb->links,
		indexes => array(
			array(indname => 'link_category', columns => 'link_category'),
			array(indname => 'link_visible', columns => 'link_visible')
		)
	),
	array(
		tabname => $wpdb->options,
		indexes => array(
			array(indname => 'option_name', columns => 'option_name')
		)
	),
	array(
		tabname => $wpdb->post2cat,
		indexes => array(
			array(indname => 'post_id', columns => 'post_id, category_id')
		)
	),
	array(
		tabname => $wpdb->postmeta,
		indexes => array(
			array(indname => 'post_id', columns => 'post_id'),
			array(indname => 'meta_key', columns => 'meta_key')
		)
	),
	array(
		tabname => $wpdb->posts,
		indexes => array(
			array(indname => 'post_name', columns => 'post_name'),
			array(indname => 'post_status', columns => 'post_status'),
			array(indname => 'type_status_date', columns => 'post_type, post_status, post_date, ID')
		)
	),
	array(
		tabname => $wpdb->users,
		indexes => array(
			array(indname => 'user_login_key', columns => 'user_login'),
			array(indname => 'user_nicename', columns => 'user_nicename')
		)
	),
	array(
		tabname => $wpdb->usermeta,
		indexes => array(
			array(indname => 'user_id', columns => 'user_id'),
			array(indname => 'meta_key', columns => 'meta_key')
		)
	)
);


// Alternative indexes
$dbt_alt_indexes = array(
	array(
		tabname => $wpdb->comments,
		indexes => array(
			array(indname => 'comment_post_ID', columns => 'comment_post_ID,comment_approved')
		)
	),
	array(
		tabname => $wpdb->links,
		indexes => array(
			array(indname => 'link_visible', columns => 'link_visible,link_category')
		)
	),
	array(
		tabname => $wpdb->postmeta,
		indexes => array(
			array(indname => 'post_id', columns => 'post_id,meta_key')
		)
	),
	array(
		tabname	=> $wpdb->posts,
		indexes	=> array(
			array(indname => 'post_status', columns => 'post_status, post_date, ID')
		)
	)
);


// definition tables
$dbt_tables = array(
	$wpdb->terms,
	$wpdb->term_taxonomy,
	$wpdb->term_relationships,
	$wpdb->posts,
	$wpdb->users,
	$wpdb->categories,
	$wpdb->post2cat,
	$wpdb->comments,
	$wpdb->link2cat,
	$wpdb->links,
	$wpdb->linkcategories,
	$wpdb->options,
	$wpdb->postmeta,
	$wpdb->usermeta
);



// ============================================================
// utility functions
// ============================================================

// remove unset tablenames (for WP versions before 2.1/2.3)
function remove_unset_tabnames() {
	global $wpdb, $dbt_tables, $dbt_wp_indexes, $dbt_wp_alt_indexes;

	$existing_tables	= $wpdb->get_results('SHOW TABLES', ARRAY_N);
	$existing_tables	= array_map(create_function('$t', 'return $t[0];'), $existing_tables);

	for ($j = count($dbt_tables) - 1; $j >= 0; $j--) {
		if ( empty($dbt_tables[$j]) || ! in_array($dbt_tables[$j], $existing_tables) ) {
			array_splice($dbt_tables, $j, 1);
		}
	}

	for ($j = count($dbt_wp_indexes) - 1; $j >= 0; $j--) {
		if ( empty($dbt_wp_indexes[$j]['tabname']) || ! in_array($dbt_wp_indexes[$j]['tabname'], $existing_tables) ) {
			array_splice($dbt_wp_indexes, $j, 1);
		}
	}

	for ($j = count($dbt_wp_alt_indexes) - 1; $j >= 0; $j--) {
		if ( empty($dbt_wp_alt_indexes[$j]['tabname']) || ! in_array($dbt_wp_alt_indexes[$j]['tabname'], $existing_tables) ) {
			array_splice($dbt_wp_alt_indexes, $j, 1);
		}
	}
}

// print header
function dbt_header($s) {
	echo "<p><strong>$s:</strong></p>\n";
}

// print item
function dbt_item($s) {
	echo "<li>$s</li>\n";
}



// ============================================================
// database functions
// ============================================================

// get table information
function dbt_get_table_infos($tabs) {
	global $wpdb;
	$tabinfos	= array();

	foreach($tabs as $tab) {
		$results = $wpdb->get_results("SHOW TABLE STATUS LIKE '{$tab}'");
		if ( isset($results) ) {
			$tabinfos[$tab]	= $results[0];
		}
	}
	$wpdb->query('COMMIT');

	return $tabinfos;
}


// create indexes
function dbt_create_indexes($dbt_indexes) {
	global $wpdb;

	$ret	= false;

	dbt_header(dbt__('Indizes anlegen/Ergebnisse'));
	echo '<ul>';

	foreach($dbt_indexes as $tab) {

		$existingtabix = $wpdb->get_results("SHOW INDEX FROM {$tab['tabname']}");

		foreach($tab['indexes'] as $tabix) {
			$found = false;
			$first_column	= '';

			foreach($existingtabix as $eix) {

				if ( $eix->Key_name == $tabix['indname'] ) {
					$found = true;
				}

				if ( $eix->Seq_in_index == 1 ) {
					$first_column	= $eix->Column_name;
				}

				if ( $found && !empty($first_column) ) {
					break;
				}
			}

			if ( !$found ) {

				$cols	= preg_split("/\s*,\s*/", $tabix['columns']);

				if ( $cols[0] == $first_column ) {

					dbt_item(sprintf(dbt__('Ein Index f&uuml;r die Spalte %s der Tabelle %s ist bereits vorhanden.'), $cols[0], $tab['tabname']));

				} else {

					$tabcols	= array_map(create_function('$t', 'return $t->Field;'),
									$wpdb->get_results("SHOW COLUMNS FROM {$tab['tabname']}"));
					if ( count(array_diff($cols, $tabcols)) == 0 ) {

						$results = $wpdb->get_results("ALTER TABLE {$tab['tabname']} ADD INDEX {$tabix['indname']} ({$tabix['columns']})");
						if ( isset($results) && count($results) > 0 ) {
							foreach($results as $r) {
								dbt_item("{$r->Table}: {$r->Msg_type} &rarr; {$r->Msg_text}");
							}
						} else {
							dbt_item(sprintf(dbt__('Der Index %s f&uuml;r Tabelle %s wurde angelegt.'), $tabix['indname'], $tab['tabname']));

							$ret	= true;
						}
					} else {
						dbt_item(sprintf(dbt__('Der Index %s enth&auml;lt Spalten, die in der Tabelle %s nicht vorhanden sind.'), $tabix['indname'], $tab['tabname']));
					}

				}

			} else {
				dbt_item(sprintf(dbt__('Der Index %s f&uuml;r Tabelle %s ist bereits vorhanden.'), $tabix['indname'], $tab['tabname']));
			}
		}
	}
	$wpdb->query('COMMIT');
	echo "</ul>\n";

	return $ret;
}


// drop indexes
function dbt_drop_indexes($dbt_indexes) {
	global $wpdb;

	dbt_header(dbt__('Indizes entfernen/Ergebnisse'));
	echo '<ul>';

	foreach($dbt_indexes as $tab) {
		$existingtabix = $wpdb->get_results("SHOW INDEX FROM {$tab['tabname']}");

		foreach($tab['indexes'] as $tabix) {
			$found = false;

			foreach($existingtabix as $eix) {
				if ( $eix->Key_name == $tabix['indname'] ) {
					$found = true;
					break;
				}
			}

			if ( $found ) {

				$results = $wpdb->get_results("ALTER TABLE {$tab['tabname']} DROP INDEX {$tabix['indname']}");
				if ( isset($results) && count($results) > 0 ) {
					foreach($results as $r) {
						dbt_item("{$r->Table}: {$r->Msg_type} &rarr; {$r->Msg_text}");
					}
				} else {
					dbt_item(sprintf(dbt__('Der Index %s f&uuml;r Tabelle %s wurde entfernt.'), $tabix['indname'], $tab['tabname']));
				}

			} else {
				dbt_item(sprintf(dbt__('Der Index %s f&uuml;r Tabelle %s ist nicht vorhanden.'), $tabix['indname'], $tab['tabname']));
			}
		}
	}
	$wpdb->query('COMMIT');
	echo "</ul>\n";
}


// table action
function dbt_action_tables($cmd, $tabs, $allowed, $tabinfos) {
	global $wpdb;

	echo '<ul>';

	foreach($tabs as $tab) {
		if(in_array($tabinfos[$tab]->Engine, $allowed)) {
			$results = $wpdb->get_results(sprintf($cmd, $tab));
			if ( isset($results) ) {
				foreach($results as $r) {
					dbt_item("{$r->Table}: {$r->Msg_type} &rarr; {$r->Msg_text}");
				}
			}
		} else {
			dbt_item(sprintf(dbt__('Die Tabelle %s nutzt die Speicher-Engine %s und kann daher nicht bearbeitet werden.'), $tab, empty($tabinfos[$tab]->Engine) ? $tabinfos[$tab]->Engine : '?'));
		}
	}
	$wpdb->query('COMMIT');

	echo "</ul>\n";
}


// table action
function dbt_info_tables($tabs, $tabinfos) {

	$todo	= '';

	echo "<table>\n<thead>" .
		'<tr><th>' . dbt__('Tabellenname') .
		'</th><th>' . dbt__('Speicher-Engine') .
		'</th><th>' . dbt__('Anzahl Datens&auml;tze') .
		'</th><th>' . dbt__('Durchschnittliche Datensatzl&auml;nge in Byte') .
		'</th><th>' . dbt__('Zeitpunkt der Tabellenerstellung') .
		'</th><th>' . dbt__('Zeitpunkt der letzten Aktualisierung') .
		'</th><th>' . dbt__('Zeitpunkt der letzten Tabellenpr&uuml;fung') .
		'</th><th>' . dbt__('Kommentar zur Tabelle') . "</th></tr>\n" .
		"</thead>\n<tbody>";

	foreach($tabs as $tab) {
		echo "<tr><td>{$tabinfos[$tab]->Name}</td><td>{$tabinfos[$tab]->Engine}</td><td>{$tabinfos[$tab]->Rows}</td><td>{$tabinfos[$tab]->Avg_row_length}</td><td>{$tabinfos[$tab]->Create_time}</td><td>{$tabinfos[$tab]->Update_time}</td><td>{$tabinfos[$tab]->Check_time}</td><td>{$tabinfos[$tab->Comment]}</td></tr>\n";
		if ( $tabinfos[$tab]->Data_free > 0 ) {
			$todo	.= (empty($todo) ? '' : ', ') . $tabinfos[$tab]->Name;
		}
	}

	echo "</tbody>\n</table>\n";

	if ( !empty($todo) ) {
		echo '<p><strong>'. dbt__('Diese Tabelle(n) solltest du optimieren') . ": $todo</strong></p>";
	}
}


// check tables
function dbt_check_tables($tabs, $allowed, $tabinfos) {
	dbt_header(dbt__('Tabellen pr&uuml;fen/Ergebnisse'));
	dbt_action_tables('CHECK TABLE %s EXTENDED', $tabs, $allowed, $tabinfos);
}


// optimize tables
function dbt_optimize_tables($tabs, $allowed, $tabinfos) {
	dbt_header(dbt__('Tabellen optimieren/Ergebnisse'));
	dbt_action_tables('OPTIMIZE TABLE %s', $tabs, $allowed, $tabinfos);
}


// analyze tables
function dbt_analyze_tables($tabs, $allowed, $tabinfos) {
	dbt_header(dbt__('Tabellen analysieren/Ergebnisse'));
	dbt_action_tables('ANALYZE TABLE %s', $tabs, $allowed, $tabinfos);
}


// repair tables
function dbt_repair_tables($tabs, $allowed, $tabinfos) {
	dbt_header(dbt__('Tabellen reparieren/Ergebnisse'));
	dbt_action_tables('REPAIR TABLE %s EXTENDED', $tabs, $allowed, $tabinfos);
}


// extended repair tables
function dbt_extendedrepair_tables($tabs, $allowed, $tabinfos) {
	dbt_header(dbt__('Tabellen mit Hilfe der Formatdatei reparieren/Ergebnisse'));
	dbt_action_tables('REPAIR TABLE %s EXTENDED USE_FRM', $tabs, $allowed, $tabinfos);
}


// delete spam
function dbt_delete_spam() {
	global $wpdb;

	dbt_header(dbt__('Spam l&ouml;schen/Ergebnisse'));
	echo '<ul>';

	$deleted	= $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
	if ( isset($deleted) ) {
		if( $deleted == 0 ) {
			dbt_item(dbt__('Keine Datens&auml;tze vorhanden'));
		} else {
			dbt_item( ( $deleted == 1 ) ?
				dbt__('Der gew&uuml;nschte Datensatz wurden gel&ouml;scht.') :
				sprintf(dbt__("Die gew&uuml;nschten %s Datens&auml;tze wurden gel&ouml;scht."), $deleted));
		}
	} else {
		dbt_item(dbt__('Es wurden keine Datens&auml;tze gefunden.'));
	}
	$wpdb->query('COMMIT');

	echo "</ul>\n";

	return (isset($deleted) && ($deleted > 0));
}


// delete spam
function dbt_check_indexes($tabs) {
	global $wpdb;
	$tabsfound	= array();

	dbt_header(dbt__('Indizes pr&uuml;fen/Ergebnisse'));

	foreach($tabs as $tab) {
		$cols	= array();

		$indexes = $wpdb->get_results("SHOW INDEX FROM {$tab};");

		foreach($indexes as $ix) {

			if ( $ix->Seq_in_index == 1 ) {
				if ( array_key_exists($ix->Column_name, $cols) ) {
					array_push($cols[$ix->Column_name], $ix->Key_name);
				} else {
					$cols[$ix->Column_name]	= array($ix->Key_name);
				}
			}
		}

		foreach($cols as $key => $ixnames) {
			if ( count($cols[$key]) > 1 ) {
				$tabsfound[$tab][$key]	= $ixnames;
			}
		}
	}

	$wpdb->query('COMMIT');
	echo '<ul>';
	if ( count($tabsfound) > 0 ) {
		foreach($tabsfound as $tkey => $tvar) {
			foreach($tvar as $tvkey => $tvvar) {
				dbt_item(sprintf(dbt__('In der Tabelle %s ist die Spalte %s in folgenden Indizes als erste Spalte vorhanden: %s'), $tkey, $tvkey, implode(', ', $tvvar)));
			}
		}
	} else {
		dbt_item(dbt__('Keine &uuml;berfl&uuml;ssigen Indizes erkannt.'));
	}
	echo "</ul>\n";
}


// get table from index structure
function dbt_idx2tab($t) {
	return $t['tabname'];
}



// ============================================================
// scheduling
// ============================================================

// interval for scheduling
function dbt_more_recurences() {
	return array(
		'dbtautooptimize'	=> array(
									'interval' => _dbt_get_option(DBT_OPTION_OPTIMIZE, DBT_OPTION_OPTIMIZE_DEF) * DBT_ONEDAY,
									'display' => 'Special interval for optimizing tables'
								),
	);
}


// schedule function: optimize all tables where Data_free is not 0
function dbt_schedule_optimize() {
	global $wpdb, $dbt_tables;

	$tabs	= array();
	$error		= false;
	$lasterrmsg	= dbt__('Keine Tabellen optimiert.');

	remove_unset_tabnames();

	// Get all tables to be optimized
	foreach($dbt_tables as $tab) {
		$results = $wpdb->get_results("SHOW TABLE STATUS LIKE '{$tab}'");
		if ( isset($results) ) {
			if ( ($results[0]->Data_free > 0) && in_array($results[0]->Engine, array('MyISAM', 'InnoDB'))) {
				array_push($tabs, $results[0]->Name);
 			}
		}
	}
	$wpdb->query('COMMIT');

	// optimize tables
	if(count($tabs) > 0) {
		$lasterrmsg	= sprintf(dbt__('Folgende Tabelle(n) wurden optimiert: %s'), implode(', ', $tabs));
	 	foreach($tabs as $tab) {
 			$results	= $wpdb->get_results(sprintf('OPTIMIZE TABLE %s', $tab));
			if ( isset($results) ) {
				foreach($results as $r) {
					if ( $r->Msg_type == 'error' ) {
						$lasterrmsg	= $r->Msg_text;
						$error	= true;
 					}
				}

				if ( ! $error ) {
					$results	= $wpdb->get_results(sprintf('ANALYZE TABLE %s', $tab));
					foreach($results as $r) {
						if ( $r->Msg_type == 'error' ) {
							$lasterrmsg	= $r->Msg_text;
							$error	= true;
						}
					}
				}
 			}
 		}
		$wpdb->query('COMMIT');
 	}

	// store result
	_dbt_set_option(date(get_option('date_format') . ' ' . get_option('time_format')) . ': ' .
			($error ?
				dbt__(sprintf('Zuletzt aufgetretener Fehler: %s', $lasterrmsg)) :
				$lasterrmsg),
		'dbt_auto_optimize_rc',
		'Information about last optimization of tables',
		'');
}


// schedule function: remove ip address and user agent
function dbt_schedule_removeip() {
	global $wpdb;

	$thedays	= _dbt_get_option(DBT_OPTION_REMOVE_IP, DBT_OPTION_REMOVE_IP_DEF);
	$updated	= $wpdb->query("UPDATE $wpdb->comments SET comment_author_IP = '', comment_agent = '', comment_author_url = CASE comment_author_url WHEN 'http://' THEN '' ELSE comment_author_url END WHERE comment_date_gmt < TIMESTAMPADD(DAY, -{$thedays}, CURRENT_TIMESTAMP()) AND (comment_author_IP = '' OR comment_agent = '' OR comment_author_url = 'http://')");
	$wpdb->query('COMMIT');

	// store result
	_dbt_set_option(date(get_option('date_format') . ' ' . get_option('time_format')) . ': ' .
			((isset($updated) && ($updated >= 0)) ?
				sprintf(dbt__("%s Datensatz/-s&auml;tze aktualisiert"), $updated) :
				dbt__('Keinen Datensatz aktualisiert')),
		'dbt_auto_removeip_rc',
		'Information about last removing of ip addresses and user agents',
		'');
}


// schedule function: delete comments marked as 'spam'
function dbt_schedule_deletespam() {
	global $wpdb;

	$thedays	= _dbt_get_option(DBT_OPTION_DELETE_SPAM, DBT_OPTION_DELETE_SPAM_DEF);

 	$deleted	= $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam' AND comment_date_gmt < TIMESTAMPADD(DAY, " . (-$thedays) . ', CURRENT_TIMESTAMP())');
	$wpdb->query('COMMIT');

	// store result
	_dbt_set_option(date(get_option('date_format') . ' ' . get_option('time_format')) . ': ' .
			((isset($deleted) && ($deleted >= 0)) ?
				sprintf(dbt__("%s Datensatz/-s&auml;tze gel&ouml;scht"), $deleted) :
				dbt__('Keinen Datensatz gel&ouml;scht')),
		'dbt_auto_deletespam_rc',
		'Information about last deleting of spam',
		'');
}


// set scheduling (remove old scheduling, set new scheduling)
function dbt_schedule($opt, $quiet = false) {
	if ( ! function_exists('wp_schedule_event') ) {
		return;
	}

	if ( ! $opt[DBT_OPTION_ACTIVE] ) {
		return;
	}

	$t	= time();
	$t	-= $t % DBT_ONEDAY;

	// optimize

	// clear hook if exists
	if ( wp_next_scheduled('dbt_auto_optimize_hook') ) {
		wp_clear_scheduled_hook( 'dbt_auto_optimize_hook' );
		if ( !$quiet ) {
			dbt_item(dbt__('Alte automatische Optimierung entfernt'));
		}
	}

	// set hook (5.05 a.m.)
	if ( $opt[DBT_OPTION_OPTIMIZE] > 0 ) {
		if ( ! wp_next_scheduled('dbt_auto_optimize_hook') ) {
			wp_schedule_event( $t + 18300, 'dbtautooptimize', 'dbt_auto_optimize_hook' );
			if ( !$quiet ) {
				dbt_item(dbt__('Neue automatische Optimierung eingetragen'));
			}
		}
	}

	// remove ip and user agent

	// clear hook if exists
	if ( wp_next_scheduled('dbt_auto_removeip_hook') ) {
		wp_clear_scheduled_hook( 'dbt_auto_removeip_hook' );
		if ( !$quiet ) {
			dbt_item(dbt__('Alte automatisches Entfernen von IP-Adresse und User-Agent entfernt'));
		}
	}

 	// set hook (4.05 a.m.)
	if ( $opt[DBT_OPTION_REMOVE_IP] > 0 ) {
		if ( ! wp_next_scheduled('dbt_auto_removeip_hook') ) {
			wp_schedule_event( $t + 14700, 'daily', 'dbt_auto_removeip_hook' );
			if ( !$quiet ) {
				dbt_item(dbt__('Neue automatisches Entfernen von IP-Adresse und User-Agent eingetragen'));
			}
		}
	}

	// delete spam

	// clear hook if exists
	if ( wp_next_scheduled('dbt_auto_deletespam_hook') ) {
		wp_clear_scheduled_hook( 'dbt_auto_deletespam_hook' );
		if ( !$quiet ) {
			dbt_item(dbt__('Alte automatische L&ouml;schung von Spam-Kommentaren entfernt'));
		}
	}

 	// set hook (3.05 a.m.)
	if ( $opt[DBT_OPTION_DELETE_SPAM] > 0 ) {
		if ( ! wp_next_scheduled('dbt_auto_deletespam_hook') ) {
			wp_schedule_event( $t + 11100, 'daily', 'dbt_auto_deletespam_hook' );
			if ( !$quiet ) {
				dbt_item(dbt__('Neue automatische L&ouml;schung von Spam-Kommentaren eingetragen'));
			}
		}
	}
}



// ============================================================
// options
// ============================================================

// set an option
function _dbt_set_option($from_post, $optname, $optdesc, $empt) {
	$opt	= isset($from_post) ? $from_post : $empt;

	if ( get_option($optname) === false )
		add_option($optname, $opt, $optdesc);
	else
		update_option($optname, $opt);

	return $opt;
}


// get an option
function _dbt_get_option($optname, $def) {
	$opt = get_option($optname);

	if ( $opt === false )
		$opt	= $def;

	return $opt;
}


// get options
function dbt_getoptions() {
	return array(
		dbt_auto_optimize	=> _dbt_get_option(DBT_OPTION_OPTIMIZE, DBT_OPTION_OPTIMIZE_DEF),
		dbt_auto_deletespam	=> _dbt_get_option(DBT_OPTION_DELETE_SPAM, DBT_OPTION_DELETE_SPAM_DEF),
		dbt_auto_removeip	=> _dbt_get_option(DBT_OPTION_REMOVE_IP, DBT_OPTION_REMOVE_IP_DEF),
		dbt_active		=> _dbt_get_option(DBT_OPTION_ACTIVE, DBT_OPTION_ACTIVE_DEF)
	);
}


// set options
function dbt_setoptions() {

	dbt_header(dbt__('Optionen setzen'));

	echo '<ul>';

	$opt	= array(
		dbt_auto_optimize	=> _dbt_set_option($_POST[DBT_OPTION_OPTIMIZE], DBT_OPTION_OPTIMIZE, 'Interval used for optimizing tables', DBT_OPTION_OPTIMIZE_DEF),
		dbt_auto_deletespam	=> _dbt_set_option($_POST[DBT_OPTION_DELETE_SPAM], DBT_OPTION_DELETE_SPAM, 'Interval used for deleting comments marked as spam', DBT_OPTION_DELETE_SPAM_DEF),
		dbt_auto_removeip	=> _dbt_set_option($_POST[DBT_OPTION_REMOVE_IP], DBT_OPTION_REMOVE_IP, 'Interval used for deleting ip addresses and user agents', DBT_OPTION_REMOVE_IP_DEF),
		dbt_active		=> _dbt_set_option(true, DBT_OPTION_ACTIVE, 'Flag for activating options', DBT_OPTION_ACTIVE_DEF)
	);

	dbt_item(dbt__('Optionen gespeichert'));

	dbt_schedule($opt);

	echo "</ul>\n";

	return $opt;
}



// ============================================================
// formular
// ============================================================

// one option cell
function dbt_option_cell($name, $shorttext, $longtext, $extratext = '') {

	if ( isset($extratext) && !empty($extratext) ) {
		$extratext	= '<br /><strong>' . $extratext . '</strong>';
	} else {
		$extratext	= '';
	}

	echo "<td><input type=\"radio\" id=\"dbt_{$name}\" name=\"aktion\" value=\"{$name}\" /></td>
		<td><label for=\"dbt_{$name}\">{$shorttext}</label><br /><small>{$longtext}{$extratext}</small></td>";
}


// one option row
function dbt_option_row($name, $shorttext, $longtext, $extratext = '') {
	echo '<tr valign="middle">';
	dbt_option_cell($name, $shorttext, $longtext, $extratext);
	echo '</tr>';
}


// text for value
function dbt_valtext($val) {
	if ( $val == 0 ) {
		return dbt__('Nie');
	}

	if ( $val % 7 == 0 ) {
		$wochen	= $val / 7;
		return ($wochen == 1) ? ('1 ' . dbt__('Woche')) : ("$wochen " . dbt__('Wochen'));
	}

	return ($val == 1) ? ('1 ' . dbt__('Tag')) : ("$val " . dbt__('Tage'));
}



// ============================================================
// administration
// ============================================================

// admin page
function dbt_admin() {
	global $wpdb;
	global $dbt_tables;
	global $dbt_indexes;
	global $dbt_wp_indexes;
	global $dbt_alt_indexes;
	$opt	= array();

	remove_unset_tabnames();

	$selsize	= count($dbt_tables) + 2;
	$selsize	= ($selsize < 14) ? 14 : $selsize;

	if ( $_POST['aktion'] != 'setoptions' ) {
		$opt	= dbt_getoptions();
	}
?>
	<div class="wrap">
		<h2>Database Tuning</h2>
		<p><?php dbt_e('Mit <em>Database Tuning</em> k&ouml;nnen die Tabellen ein wenig optimiert werden, damit diverse Lesezugriffe performanter werden.<br />Die Analyse sollte regelm&auml;&szlig;ig erfolgen, die Optimierung sp&auml;testens nach gr&ouml;&szlig;eren Datenbestands&auml;nderungen.<br />Die Indizes helfen, Daten schneller zu lesen und sollten daher auch den Aufbau der Seiten positiv beeinflussen.<br />Anmerkung: Es kann durchaus sein, dass nach einem WordPress-Update die zus&auml;tzlichen Indizes einmal entfernt und wieder neu angelegt werden m&uuml;ssen, da das WordPress-Update selbst neue Indizes anlegt. Das Plugin achtet darauf, dass keine &uuml;berfl&uuml;ssigen zus&auml;tzlichen Indizes angelegt werden.'); ?><br />
		<?php dbt_e('Als kleiner Tipp f&uuml;r die Vorgehensweise: Die zus&auml;tzlichen und die alternativen Indizes verbessern die Leseperformance und empfehlen sich daher. Wartungsarbeiten wie die Optimierung der Tabellen empfehlen sich sp&auml;testens nach gr&ouml;&szlig;eren Daten&auml;nderungen oder dem L&ouml;schen von Datens&auml;tzen. Die Analyse sollte regelm&auml;&szlig;ig durchgef&uuml;hrt werden.'); ?><br />
		<b><?php dbt_e('Sehr wichtiger Hinweis: Regelm&auml;&szlig;ig und erst recht vor Aktionen &ndash;&nbsp;insbesondere der Optimierung&nbsp;&ndash; immer ein Backup (Datensicherung) machen!<br />Es ist keine Funktionalit&auml;t f&uuml;r Backup oder Restore enthalten!') ?></b></p>
<?php
	if ( isset($_POST['aktion']) &&
		in_array($_POST['aktion'], array('infotables', 'checktables', 'optimizetables', 'analyzetables', 'repairtables', 'extendedrepairtables', 'createaltwpindexes', 'createwpindexes', 'createindexes', 'dropindexes', 'deletespam', 'checkindexes', 'setoptions')) ) {

		echo '<hr /><p>';
		switch($_POST['aktion']) {
		case 'infotables':
			dbt_e('Tabellenstatus anzeigen');
			break;
		case 'checktables':
			dbt_e('Pr&uuml;fung der Tabellen');
			break;
		case 'optimizetables':
			dbt_e('Optimierung der Tabellen');
			break;
		case 'repairtables':
			dbt_e('Reparatur der Tabellen');
			break;
		case 'extendedrepairtables':
			dbt_e('Erweiterte Reparatur der Tabellen');
			break;
		case 'analyzetables':
			dbt_e('Analyse der Tabellen');
			break;
		case 'createaltwpindexes':
			dbt_e('Austausch der WordPress-Indizes gegen alternative Indizes');
			break;
		case 'createwpindexes':
			dbt_e('Anlegen der WordPress-Indizes');
			break;
		case 'createindexes':
			dbt_e('Anlegen der zus&auml;tzlichen Indizes');
			break;
		case 'dropindexes':
			dbt_e('Entfernen der zus&auml;tzlichen Indizes');
			break;
		case 'deletespam':
			dbt_e('Spam l&ouml;schen');
			break;
		case 'checkindexes':
			dbt_e('Erkennen &uuml;berfl&uuml;ssiger Indizes');
			break;
		case 'setoptions':
			dbt_e('Optionen aktualisieren');
			break;
		}
		echo "&nbsp;&hellip;</p>\n";

		switch($_POST['aktion']) {
		case 'infotables':
			$tabinfos	= dbt_get_table_infos($_POST['dbt_seltables']);
			dbt_info_tables($_POST['dbt_seltables'], $tabinfos);
			break;
		case 'checktables':
			$tabinfos	= dbt_get_table_infos($_POST['dbt_seltables']);
			dbt_check_tables($_POST['dbt_seltables'], array('MyISAM', 'InnoDB', 'ARCHIVE'), $tabinfos);
			break;
		case 'optimizetables':
			$tabinfos	= dbt_get_table_infos($_POST['dbt_seltables']);
			dbt_optimize_tables($_POST['dbt_seltables'], array('MyISAM', 'InnoDB', 'BDB'), $tabinfos);
			dbt_analyze_tables($_POST['dbt_seltables'], array('MyISAM', 'InnoDB'), $tabinfos);
			break;
		case 'analyzetables':
			$tabinfos	= dbt_get_table_infos($_POST['dbt_seltables']);
			dbt_analyze_tables($_POST['dbt_seltables'], array('MyISAM', 'InnoDB'), $tabinfos);
			break;
		case 'repairtables':
			$tabinfos	= dbt_get_table_infos($_POST['dbt_seltables']);
			dbt_repair_tables($_POST['dbt_seltables'], array('MyISAM', 'ARCHIVE'), $tabinfos);
			break;
		case 'extendedrepairtables':
			$tabinfos	= dbt_get_table_infos($_POST['dbt_seltables']);
			dbt_extendedrepair_tables($_POST['dbt_seltables'], array('MyISAM', 'ARCHIVE'), $tabinfos);
			break;
		case 'createaltwpindexes':
			dbt_drop_indexes($dbt_alt_indexes);
			if ( dbt_create_indexes($dbt_alt_indexes) ) {
				$indtables	= array_map(dbt_idx2tab, $dbt_alt_indexes);
				$tabinfos	= dbt_get_table_infos($indtables);
				dbt_analyze_tables($indtables, array('MyISAM', 'InnoDB'), $tabinfos);
			}
			break;
		case 'createwpindexes':
			dbt_drop_indexes($dbt_alt_indexes);
			if ( dbt_create_indexes($dbt_wp_indexes) ) {
				$indtables	= array_map(dbt_idx2tab, $dbt_wp_indexes);
				$tabinfos	= dbt_get_table_infos($indtables);
				dbt_analyze_tables($indtables, array('MyISAM', 'InnoDB'), $tabinfos);
			}
			break;
		case 'createindexes':
			if ( dbt_create_indexes($dbt_indexes) ) {
				$indtables	= array_map(dbt_idx2tab, $dbt_indexes);
				$tabinfos	= dbt_get_table_infos($indtables);
				dbt_analyze_tables($indtables, array('MyISAM', 'InnoDB'), $tabinfos);
			}
			break;
		case 'dropindexes':
			dbt_drop_indexes($dbt_indexes);
			break;
		case 'deletespam':
			if ( dbt_delete_spam() ) {
				$tabinfos	= dbt_get_table_infos(array($wpdb->comments));
				dbt_optimize_tables(array($wpdb->comments), array('MyISAM', 'InnoDB', 'BDB'), $tabinfos);
				dbt_analyze_tables(array($wpdb->comments), array('MyISAM', 'InnoDB'), $tabinfos);
			}
			break;
		case 'checkindexes':
			dbt_check_indexes($dbt_tables);
			break;
		case 'setoptions':
			$opt	= dbt_setoptions();
			break;
		}

		echo "<p>&hellip;&nbsp;" . dbt__("fertig.") . "</p><hr />\n";

	}
?>

		<form action="<?php echo $_SERVER[REQUEST_URI] ?>" method="post">
			<fieldset class="options">
				<legend><?php dbt_e('Erst einmal&nbsp;&hellip;'); ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				<tbody>
					<tr valign="middle">
						<td><input type="radio" id="dbt_nothing" name="aktion" value="nothing" checked="checked" /></td>
						<td><label for="dbt_nothing"><?php dbt_e('Keine Aktion'); ?></label></td>
					</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="options">
				<legend><?php dbt_e('Tabellen- und Indexwartung'); ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				<tbody>
					<tr valign="middle">
						<td rowspan="6" valign="top"><label for="dbt_seltables"><?php dbt_e('Auswahl'); ?>:</label><br /><select name="dbt_seltables[]" id="dbt_seltables" multiple="multiple" size="<?php
							echo $selsize;
						?>" style="height: <?php
							echo $selsize;
						?>em;">
						<optgroup label="<?php dbt_e('WordPress-Tabellen'); ?>">
						<?php
							foreach($dbt_tables as $tab) {
								echo "<option value=\"{$tab}\" selected=\"selected\">$tab</option>\n";
							}
						?>
						</optgroup>
						<?php
							$other_tables	= $wpdb->get_results('SHOW TABLES', ARRAY_N);
							$other_tables	= array_map(create_function('$t', 'return $t[0];'), $other_tables);
							$other_tables	= array_diff($other_tables, $dbt_tables);
							if ( count($other_tables) > 0 ) {
								echo '<optgroup label="' . dbt__('Andere Tabellen') . '">';
								foreach($other_tables as $tab) {
									echo "<option value=\"{$tab}\">{$tab}</option>\n";
								}
								echo '</optgroup>';
							}
						?>
						</select></td>
						<?php
							dbt_option_cell('infotables', dbt__('Tabellenstatus anzeigen'), dbt__('Status der Tabellen anzeigen'));
						?>
					</tr>
					<?php
						dbt_option_row('checktables', dbt__('Tabellen pr&uuml;fen'), dbt__('Tabellen auf Fehler pr&uuml;fen und Schl&uuml;sselstatistiken aktualisieren'));
						dbt_option_row('optimizetables', dbt__('Tabellen optimieren'), dbt__('Nicht verwendeten Speicher in der Tabelle freigeben, Datendatei defragmentieren, Schl&uuml;sselanalyse durchf&uuml;hren und Indexbaum sortieren (Optimierung)'));
						dbt_option_row('analyzetables', dbt__('Tabellen analysieren'), dbt__('Schl&uuml;sselverteilung der Tabellen analysieren und speichern (Analyse)'));
						dbt_option_row('repairtables', dbt__('Tabellen reparieren'), dbt__('Repariert unter Umst&auml;nden Tabellen'), dbt__('Nur f&uuml;r wirklich defekte Tabellen ausgef&uuml;hren!'));
						dbt_option_row('extendedrepairtables', dbt__('Tabellen mit Hilfe der Formatdatei reparieren'), dbt__('Repariert unter Umst&auml;nden Tabellen unter Ausnutzung der Formatdatei'), dbt__('Nur ausf&uuml;hren, wenn die normale Reparatur fehlgeschlagen ist!'));
					?>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="options">
				<legend><?php dbt_e('WordPress-Indizes'); ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				<tbody>
					<?php
						dbt_option_row('createaltwpindexes', dbt__('Alternative WordPress-Indizes anlegen'), dbt__('Legt alternative WordPress-Indizes (mit zus&auml;tzlichen Spalten) an'));
						dbt_option_row('createwpindexes', dbt__('WordPress-Indizes anlegen'), sprintf(dbt__('Legt die urspr&uuml;nglichen WordPress-Indizes (Stand: %s) an, entfernt die Alternativen'), DBT_WPINDEXES));
					?>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="options">
				<legend><?php dbt_e('Zus&auml;tzliche Indizes'); ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				<tbody>
					<?php
						dbt_option_row('createindexes', dbt__('Zus&auml;tzliche Indizes anlegen'), dbt__('Mehrere Indizes f&uuml;r schnelleren Lesezugriff anlegen'));
						dbt_option_row('dropindexes', dbt__('Zus&auml;tzliche Indizes entfernen'), dbt__('Zus&auml;tzliche Indizes wieder entfernen'));
					?>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="options">
				<legend><?php dbt_e('Datenbereinigung'); ?></legend>
				<table cellspacing="2" cellpadding="5" class="editform">
				<tbody>
					<?php
						dbt_option_row('deletespam', dbt__('Spam l&ouml;schen'), dbt__('Als &bdquo;Spam&ldquo; gekennzeichnete Kommentare l&ouml;schen'));
						dbt_option_row('checkindexes', dbt__('&Uuml;berfl&uuml;ssige Indizes erkennen'), dbt__('Erkennt Indizes mit gleicher Spaltenliste'));
					?>
				</tbody>
				</table>
			</fieldset>
			<p class="submit"><input type="submit" name="dbt_doit" value="<?php dbt_e('Aktion ausf&uuml;hren'); ?> &#187;" /></p>
		</form>

		<?php
			/* scheduling options - begin */
			if ( function_exists('wp_schedule_event') ) {
		?>

		<hr />
		<form action="<?php echo $_SERVER[REQUEST_URI] ?>" method="post">
			<input type="hidden" name="aktion" value="setoptions" />
			<fieldset class="options">
				<legend><?php dbt_e('Regelm&auml;&szlig;ige Aktionen'); ?></legend>
				<?php
					$choices	= array(0, 3, 7, 14, 21, 28, 42, 84);
				?>
				<table cellspacing="2" cellpadding="5" class="editform">
				<tbody>
					<tr valign="middle">
						<td><label for="dbt_auto_optimize"><?php dbt_e('Tabellen optimieren'); ?>:</label></td>
						<td><select name="dbt_auto_optimize" id="dbt_auto_optimize">
						<?php
							if ( ! in_array($opt[DBT_OPTION_OPTIMIZE], $choices) ) {
								echo '<option value="' . $opt[DBT_OPTION_OPTIMIZE] . '" selected="selected">' .
										dbt_valtext($opt[DBT_OPTION_OPTIMIZE]) .
										'</option>';
							}
							foreach($choices as $val) {
								echo "<option value=\"$val\"" .
									(($val == $opt[DBT_OPTION_OPTIMIZE]) ? ' selected="selected"' : '') . '>' .
									dbt_valtext($val) .
									'</option>';
							}
						?>
						</select></td>
						<td><small>(<?php dbt_e('Ausf&uuml;hrungsintervall'); ?>)</small></td>
					</tr>
					<?php
						$rc	= _dbt_get_option('dbt_auto_optimize_rc', '');
						if ( ! empty($rc) ) {
							?>
							<tr valign="middle">
								<td></td>
								<td colspan="2"><?php dbt_e('Letztes Ergebnis:'); echo ' ' . $rc; ?></td>
							</tr>
							<?php
						}
					?>
					<tr valign="middle">
						<td><label for="dbt_auto_deletespam"><?php dbt_e('Spam l&ouml;schen'); ?></label></td>
						<td><select name="dbt_auto_deletespam" id="dbt_auto_deletespam">
							<?php
								if ( ! in_array($opt[DBT_OPTION_DELETE_SPAM], $choices) ) {
									echo '<option value="' . $opt[DBT_OPTION_DELETE_SPAM] . '" selected="selected">' .
										dbt_valtext($opt[DBT_OPTION_DELETE_SPAM]) .
										'</option>';
								}
								foreach($choices as $val) {
									echo "<option value=\"$val\"" .
										(($val == $opt[DBT_OPTION_DELETE_SPAM]) ? ' selected="selected"' : '') . '>' .
										dbt_valtext($val) .
										'</option>';
								}
							?>
						</select></td>
						<td><small>(<?php dbt_e('Abh&auml;ngig vom Eintragsdatum des Kommentars'); ?>)</small></td>
					</tr>
					<?php
						$rc	= _dbt_get_option('dbt_auto_deletespam_rc', '');
						if ( ! empty($rc) ) {
							?>
							<tr valign="middle">
								<td></td>
								<td colspan="2"><?php dbt_e('Letztes Ergebnis:'); echo ' ' . $rc; ?></td>
							</tr>
							<?php
						}
					?>
					<tr valign="middle">
							<td><label for="dbt_auto_removeip"><?php dbt_e('IP-Adresse und User-Agent entfernen'); ?>:</label></td>
							<td><select name="dbt_auto_removeip" id="dbt_auto_removeip">
							<?php
								if ( ! in_array($opt[DBT_OPTION_REMOVE_IP], $choices) ) {
									echo '<option value="' . $opt[DBT_OPTION_REMOVE_IP] . '" selected="selected">' .
										dbt_valtext($opt[DBT_OPTION_REMOVE_IP]) .
										'</option>';
								}
								foreach($choices as $val) {
									echo "<option value=\"$val\"" .
										(($val == $opt[DBT_OPTION_REMOVE_IP]) ? ' selected="selected"' : '') . '>' .
										dbt_valtext($val) .
										'</option>';
								}
							?>
							</select></td>
							<td><small>(<?php dbt_e('Abh&auml;ngig vom Eintragsdatum des Kommentars'); ?>)</small></td>
					</tr>
					<?php
						$rc	= _dbt_get_option('dbt_auto_removeip_rc', '');
						if ( ! empty($rc) ) {
							?>
							<tr valign="middle">
								<td></td>
								<td colspan="2"><?php dbt_e('Letztes Ergebnis:'); echo ' ' . $rc; ?></td>
							</tr>
							<?php
						}
					?>
				</tbody>
				</table>
			</fieldset>
				<?php if ( ! $opt[DBT_OPTION_ACTIVE] ) { ?>
					<p><?php dbt_e('Die regelm&auml;&szlig;igen Aktionen werden erst dann aktiviert, wenn mindestens einmal die Optionen gespeichert wurden.'); ?></p>
				<?php } ?>
			<p class="submit"><input type="submit" name="dbt_doit" value="<?php dbt_e('Aktualisiere Optionen'); ?> &#187;" /></p>
		</form>

		<?php
			}
			/* scheduling options - end */
		?>

	</div>
<?php
}


// add admin page
function dbt_adminpage() {
	// add a new menu
	if ( function_exists('add_management_page') )
		add_management_page('Database Tuning', 'DB-Tuning', 8, __FILE__, 'dbt_admin');
}



// ============================================================
// install and deinstall
// ============================================================

// install
function dbt_install() {
	dbt_schedule(dbt_getoptions(), true);
}

// deinstall
function dbt_deinstall() {

	if ( wp_next_scheduled('dbt_auto_optimize_hook') ) {
		wp_clear_scheduled_hook( 'dbt_auto_optimize_hook' );
 	}

	if ( wp_next_scheduled('dbt_auto_removeip_hook') ) {
		wp_clear_scheduled_hook( 'dbt_auto_removeip_hook' );
	}

	if ( wp_next_scheduled('dbt_auto_deletespam_hook') ) {
		wp_clear_scheduled_hook( 'dbt_auto_deletespam_hook' );
	}
}



// ============================================================
// actions and filter
// ============================================================

add_action('admin_menu', 'dbt_adminpage');

// scheduling

if ( function_exists('wp_schedule_event') ) {

	add_action('activate_database-tuning.php', 'dbt_install');
	add_action('deactivate_database-tuning.php', 'dbt_deinstall');

	$iscron	= false;

	$thedays	= _dbt_get_option(DBT_OPTION_OPTIMIZE, DBT_OPTION_OPTIMIZE);
	if ( $thedays > 0 ) {
		add_action('dbt_auto_optimize_hook', 'dbt_schedule_optimize');
		$iscron	= true;
	}

	$thedays	= _dbt_get_option(DBT_OPTION_REMOVE_IP, DBT_OPTION_REMOVE_IP_DEF);
	if ( $thedays > 0 ) {
		add_action('dbt_auto_removeip_hook', 'dbt_schedule_removeip');
		$iscron = true;
	}

	$thedays	= _dbt_get_option(DBT_OPTION_DELETE_SPAM, DBT_OPTION_DELETE_SPAM_DEF);
	if ( $thedays > 0 ) {
		add_action('dbt_auto_deletespam_hook', 'dbt_schedule_deletespam');
		$iscron = true;
	}

	if ( $iscron ) {
		add_filter('cron_schedules', 'dbt_more_recurences');
	}
}

?>
