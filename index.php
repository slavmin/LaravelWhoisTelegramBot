<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

function removeNotice($whois)
{
    return str_replace('NOTICE: The expiration date displayed in this record is the date the
registrar\'s sponsorship of the domain name registration in the registry is
currently set to expire. This date does not necessarily reflect the expiration
date of the domain name registrant\'s agreement with the sponsoring
registrar.  Users may consult the sponsoring registrar\'s Whois database to
view the registrar\'s reported date of expiration for this registration.
', '', $whois);
}

function removeTOU($whois)
{
    return str_replace('TERMS OF USE: You are not authorized to access or query our Whois
database through the use of electronic processes that are high-volume and
automated except as reasonably necessary to register domain names or
modify existing registrations; the Data in VeriSign Global Registry
Services\' ("VeriSign") Whois database is provided by VeriSign for
information purposes only, and to assist persons in obtaining information
about or related to a domain name registration record. VeriSign does not
guarantee its accuracy. By submitting a Whois query, you agree to abide
by the following terms of use: You agree that you may use this Data only
for lawful purposes and that under no circumstances will you use this Data
to: (1) allow, enable, or otherwise support the transmission of mass
unsolicited, commercial advertising or solicitations via e-mail, telephone,
or facsimile; or (2) enable high volume, automated, electronic processes
that apply to VeriSign (or its computer systems). The compilation,
repackaging, dissemination or other use of this Data is expressly
prohibited without the prior written consent of VeriSign. You agree not to
use electronic processes that are automated and high-volume to access or
query the Whois database except as reasonably necessary to register
domain names or modify existing registrations. VeriSign reserves the right
to restrict your access to the Whois database in its sole discretion to ensure
operational stability.  VeriSign may restrict or terminate your access to the
Whois database for failure to abide by these terms of use. VeriSign
reserves the right to modify these terms at any time.
', '', $whois);
}

function whois($domain)
{
    $cmd = escapeshellcmd('/usr/bin/whois').' '.escapeshellarg($domain);
    exec($cmd, $output, $return);

    if ($return !== 0) {
        return false;
    } 

    $whois = '';

    foreach ($output as $line) {
        if (preg_match('/^(#|%)/', $line)) {
            continue;
        }

        $whois .= $line."\n";
    }

    $whois = removeNotice($whois);
    $whois = removeTOU($whois);

    return $whois;
}

$domain = null;
$error = null;
$whois = null;

if (isset($_GET['domain']) === true && empty($_GET['domain']) === false) {
    if (preg_match('#^https?://(.*)$#i', $_GET['domain'], $match)) {
        header('Location: ./index.php?domain='.$match[1]);
        die();
    }

    if (preg_match('#www\.(.*)$#i', $_GET['domain'], $match)) {
        header('Location: ./index.php?domain='.$match[1]);
        die();
    }

    $_GET['domain'] = idn_to_ascii(strtolower($_GET['domain']));

    if (preg_match('/^[a-z0-9-]+\.[a-z0-9-]+$/', $_GET['domain']) === 0) {
        $error = 'Bad domain';
    } else {
        $domain = $_GET['domain'];

        $return = whois($domain);

        if ($return === false) {
            $error = 'Error in whois command';
        } else {
            $whois = trim($return);
        }
    }
}

?><!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title>Whois</title>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <style type="text/css">
        label{display:none}
        .form-inline .form-control{width:100%}
        .form-group.col-md-11,.form-group.col-xs-10{padding-left:0;}
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h1>Whois<?php if (null !== $domain) { echo ': '.htmlentities(idn_to_utf8($domain), ENT_HTML5, 'UTF-8'); } ?></h1>
                </div>
            </div>
            <?php if (null !== $error) { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger"><?php echo htmlentities($error, ENT_HTML5, 'UTF-8') ?></div>
                </div>
            </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <?php if (null === $domain) { ?>
                    <form action="" method="get" role="form" class="form-inline">
                        <div class="form-group col-md-11 col-xs-10">
                            <label for="domain">Domain</label>
                            <input type="text" name="domain" id="domain" class="form-control" autofocus placeholder="Enter domain name">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="glyphicon glyphicon-search"></i>
                        </button>
                    </form>
                    <?php } elseif (empty($whois) === false) { ?>
                    <pre><?php echo $whois ?></pre>

                    	<?php if(stripos($whois, 'PIN-RU')): ?>

                    	<a href="https://pinspb.ru/domains/contact-admin?domain=<?php echo $domain ?>">Contact <?php echo $domain ?> administrator</a>
                    	<?php endif; ?>

                    <?php } else { ?>
                    <div class="alert alert-warning">Maybe, no result...</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>
