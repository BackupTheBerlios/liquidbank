<?php

//require_once("lib/Currency.class.php");

require_once( "common/config.php" );
require_once( "common/db.php" );
require_once( 'common/Template.class.php' );

$tpl = new Template( 'templates/header.php' );
$tpl->bind('CURRENCY', $config['currency'] );
$tpl->publish();

require_once( "common/Twitbank.class.php" );

$tb = new Twitbank();

// get latest transaction id about currency
$latest_twit_id = $tb->getCurrentTwitId();
$latest_twit_id_old = $latest_twit_id;
if ($latest_twit_id > 0 ){
	$api_url = sprintf("http://search.twitter.com/search.atom?since_id=%s&q=%s", $latest_twit_id, $config["currency"] );
} else {
	$api_url = sprintf("http://search.twitter.com/search.atom?q=%s", $config["currency"] );
}

print "<p><b>Loading newer entries than $latest_twit_id from Twitter...</b></p>";

function getNextPage( $xpath ) {
	$nextpages = $xpath->query("/ns:feed/ns:link[@rel='next']");
	if (!is_null( $nextpages ) ){
		$obj = $nextpages->item(0);
		if ( !is_null( $obj ) ){
			return $obj->getAttribute("href");
		} else {
			return null;
		}
	} else {
		return null;
	}
}


/* regexps */
$re_give = "(?P<action>give|gave|RT|donne)";
$re_user = "@(?P<user>[a-zA-Z0-9_]+)";
$re_currency = "[#$]?" . $config["currency"];
$re_sep = "[^a-zA-Z0-9_]*\s+[^a-zA-Z0-9_]*";
$re_platform = "((?P<platform>tb|tw(ollars?)?){$re_sep})?";
#$re_value = "(?P<value>\d+)";
$re_value = "(?P<value>\d+|\d+[,\.]\d+)";



$regexp_give1 = "/^\s*{$re_give}{$re_sep}{$re_value}{$re_sep}{$re_currency}{$re_sep}{$re_platform}{$re_user}/i";
$regexp_give2 = "/^\s*{$re_user}{$re_sep}{$re_value}{$re_sep}{$re_currency}{$re_sep}{$re_platform}/i";

/* 
 * Prepare database queries
 */
$query_insert_user = "INSERT INTO tb_user ( id, registered ) "
. "VALUES ( :id, 0 )";
$stmt_insert_user = $dbh->prepare( $query_insert_user );

while( !is_null( $api_url ) ){
	$atom_content = file_get_contents( $api_url );

	$dom_doc = new DOMDocument();
	if( !$dom_doc->loadXML( $atom_content) ) {
		die( "Couldn't load xml..." );
	}
	$xpath = new DOMXpath( $dom_doc );
	$xpath->registerNamespace( 'ns', $dom_doc->documentElement->namespaceURI );

	foreach( $xpath->query('/ns:feed/ns:entry') as $entry ) {
		$id = $entry->getElementsByTagName("id")->item(0)->nodeValue;
		$id = preg_replace('/^.*:/','', $id);
		if ( $id > $latest_twit_id ) { $latest_twit_id = $id; }

		$author = $entry->getElementsByTagName("author")->item(0);
		$author = $author->getElementsByTagName("uri")->item(0)->nodeValue;
		$author = preg_replace('/^http:\/\/twitter.com\//', '', $author );

		$title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
		$title = preg_replace("/^'(.*)'$/", '$1', $title);

		$transaction = false;
		$user_src = null;
		$user_dst = null;
		$value = null;

		if ( preg_match( $regexp_give1, $title, $matches ) ||
			preg_match( $regexp_give2, $title, $matches ) )  
		{
				print "<pre>";
				print_r($matches);
				print "</pre>";
				$user_src = strtolower( $author );
				$user_dst = strtolower( $matches['user'] );
				$value = $matches['value'];
				$transaction = true;
				$platform = strtolower( $matches['platform'] );
		}

		// round to two decimals
		$value = round( preg_replace( '#,#', '.', $value) , '2' );

		print "<fieldset>";
		print "<legend>Entry $id</legend>";
		print "<b>&lt;$author&gt</b> ";
		print "$title<br/>\n";
		if ( $transaction ) {
			print "<b>transaction [ $user_src -> $value($platform) -> $user_dst ]</b><br/>\n";

			$stmt_insert_user->bindParam( ':id', $user_src, PDO::PARAM_STR );
			$stmt_insert_user->execute();

			$stmt_insert_user->bindParam( ':id', $user_dst, PDO::PARAM_STR );
			$stmt_insert_user->execute();

			$query = "INSERT INTO tb_transaction ( id, user_src, user_dst, amount, title ) "
				. "VALUES ( :id, :user_src, :user_dst, :value, :title )";

			$stmt = $dbh->prepare( $query );
			$stmt->bindParam( ':id', $id, PDO::PARAM_STR );
			$stmt->bindParam( ':user_src', $user_src, PDO::PARAM_STR );
			$stmt->bindParam( ':user_dst', $user_dst, PDO::PARAM_STR );
			$stmt->bindParam( ':value', $value, PDO::PARAM_STR );
			$stmt->bindParam( ':title', $dbh->quote( $title ), PDO::PARAM_STR );
			$stmt->execute();

			$query = "UPDATE tb_user SET update_stamp = current_timestamp WHERE id = :id";
			$stmt = $dbh->prepare( $query );
			$stmt->bindParam( ':id', $user_src, PDO::PARAM_STR );
			$stmt->execute();

			$query = "UPDATE tb_user SET update_stamp = current_timestamp WHERE id = :id";
			$stmt = $dbh->prepare( $query );
			$stmt->bindParam( ':id', $user_dst, PDO::PARAM_STR );
			$stmt->execute();
		}
		print "</fieldset>";
	}

	$api_url =getNextPage( $xpath );
}

$updated = $tb->setCurrentTwitId( $latest_twit_id );
if ( ! $updated ) {
	print "<p>Nothing new ... :-(</p>";
}

#print "ROOT = " . $dom_root->tagName;
// foreach entry
// get entry/id => transaction.twit_id
// get entry/author/uri => transaction.from_user
// get entry.title => transaction.text
// match pattern

require_once( "templates/footer.php" );
?>
