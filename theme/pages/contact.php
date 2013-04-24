<?php
    /**
    * Template Name: Contact Form
    * @package Helix Framework
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */
    get_header();

?>

<div id="contact-us" class="clearfix">

    <header class="entry-header">
        <h1 class="entry-title page-header"><?php the_title(); ?></h1>
        <?php edit_post_link( __( 'Edit', _THEME ) ); ?>
    </header>

    <?php the_post(); ?>

    <?php the_content(); ?>

    <?php

        if (isset($_POST['contact-email'])) {
            //form validation vars
            $formok 	= true;
            $errors 	= array();
            //sumbission data
            $ipaddress 	= $_SERVER['REMOTE_ADDR'];
            $datetime = date('d/m/Y H:i:s');
            //form data
            $name 		= $_POST['contact-name'];
            $email 		= $_POST['contact-email'];
            $subject 	= $_POST['contact-subject'];
            $message 	= $_POST['contact-message'];
            //form validation to go here....

            //validate name is not empty
            if(empty($name)){
                $formok = false;
                $errors[] = __("You have not entered a name", _THEME);
            }

            //validate email address is not empty
            if(empty($email)){
                $formok = false;
                $errors[] = __("You have not entered an email address", _THEME);
                //validate email address is valid
            }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $formok = false;
                $errors[] = __("You have not entered a valid email address", _THEME);
            }

            //validate message is not empty
            if(empty($message)){
                $formok = false;
                $errors[] = __("You have not entered a message", _THEME);
            }
            //validate message is greater than 20 charcters
            elseif(strlen($message) < 20){
                $formok = false;
                $errors[] = __("Your message must be greater than 20 characters", _THEME);
            }

            //send email if all is ok
            if($formok){
                $headers = "From: {$email}" . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $emailbody = "<p>You have recieved a new message from the enquiries form on your website.</p>
                <p><strong>Name: </strong> {$name} </p>
                <p><strong>Email Address: </strong> {$email} </p>
                <p><strong>Subject: </strong> {$subject} </p>
                <p><strong>Message: </strong> {$message} </p>
                <p>This message was sent from the IP Address: {$ipaddress} on {$datetime}</p>";
                mail(get_option('admin_email'),$subject,$emailbody,$headers);

            ?>
            <div>
                <p class="alert alert-success"><?php _e('Your message was sent to us successfully.', _THEME); ?></p>
            </div>
            <?php
            } else {
            ?>
            <div>
                <ul class="unstyled alert alert-danger">
                    <?php
                        foreach($errors as $error) {
                            echo '<li>'.$error.'</li>';
                        }
                    ?>
                </ul>
            </div>
            <?php
            }

        }
    ?>

    <form action="<?php the_permalink(); ?>" id="contact-form" class="form-horizontal" method="post">

        <div class="control-group">
            <label class="control-label" for="contact-name"><?php _e('Name', _THEME) ?> <span class="required">*</span></label>
            <div class="controls">
                <input type="text" id="contact-name" name="contact-name" value="" placeholder="<?php _e('Name', _THEME) ?>" required="required" class="input-xlarge" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="contact-email"><?php _e('Email', _THEME) ?> <span class="required">*</span></label>
            <div class="controls">
                <input type="email" id="contact-email" name="contact-email" value="" placeholder="<?php _e('Email', _THEME) ?>" required="required" class="input-xlarge" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="contact-subject"><?php _e('Subject', _THEME) ?> <span class="required">*</span></label>
            <div class="controls">
                <input type="text" id="contact-subject" name="contact-subject" value="" placeholder="<?php _e('Subject', _THEME) ?>" required="required" class="input-xlarge" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="contact-message"><?php _e('Message', _THEME) ?> <span class="required">*</span></label>
            <div class="controls">
                <textarea id="contact-message" rows="5" name="contact-message" placeholder="<?php _e('Message', _THEME) ?>" class="span4" required="required" title="Message"></textarea>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <input class="btn" id="submit-button" type="submit" name="submit" value="Submit">
            </div>
        </div>

    </form>



</div>

<?php get_footer(); ?>