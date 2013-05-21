<?php
ob_start();
?>
<div class="cffWrapper">
    <?php
    if ( isset( $_POST["submitted"] ) ) {
        processForm();
    } else {
        displayForm( array() );
    }

    function valdidateField( $fieldName, $missingFields ) {
        if ( in_array( $fieldName, $missingFields ) ) {
            echo ' class="error"';
        }
    }

    function setValue( $fieldName ) {
        if ( isset ( $_POST[$fieldName] ) ) {
            echo $_POST[$fieldName];
        }
    }

    function displayForm( $missingFields) {
        ?>
        <h2>Please enter your details below:</h2>
        <form action="" method="post">
            <div style="width: 100%; float: left;">
                <input type="hidden" name="submitted" value="1" />

                <label for="firstName"<?php valdidateField('firstName', $missingFields ); ?>><?php _e( 'First Name' ); ?></label>
                <input type="text" name="firstName" id="firstName" value="<?php setValue( "firstName" ) ?>" placeholder="Ryder" />

                <br />

                <label for="email"<?php valdidateField('email', $missingFields ); ?>><?php _e( 'Email' ); ?></label>
                <input type="text" name="email" id="email" value="<?php setValue( "email" ) ?>" placeholder="example@example.com" />

                <br />

                <label for="phoneNumber"<?php valdidateField('phoneNumber', $missingFields ); ?>><?php _e( 'Phone Number' ); ?></label>
                <input type="text" name="phoneNumber" id="phoneNumber" value="<?php setValue( "phoneNumber" ) ?>" placeholder="123-456-7890" />
                <div class="clr"></div>
                <span style="font-size: 9px;">(valid phone number = 123-456-7890)</span>

                <br />

                <label for="website"<?php valdidateField('website', $missingFields ); ?>><?php _e( 'Website' ); ?></label>
                <input type="text" name="website" id="website" value="<?php setValue( "website" ) ?>" placeholder="http://www.example.com" />

                <br />

                <label for="message"<?php valdidateField('message', $missingFields ); ?>><?php _e('Message'); ?></label>
                <br />
                <textarea name="message" id="message" rows="4" cols="50"><?php setValue( "message" ) ?></textarea>
                <div class="clr"></div>
                <?php echo '<img src="' . plugins_url( 'captcha_file.php?rand='.rand() , __FILE__ ) . '" id="captchaimg"> '; ?>
                <br />
                <label for="captcha"<?php valdidateField('captcha', $missingFields ); ?>>Enter Captcha</label>
                <input id="captcha" name="captcha" type="text" />
                <br />
                <div class="clr">
                    <input type="submit" name="submitButton" id="submitButton" value="Send Details" />
                    <input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right: 20px;" />
                </div>
            </div>
        </form>
        <div style="clear: both;"></div>
        <?php
    }

    function processForm() {
        $errorMessages = array();
        $emailPattern = "/^\w+((-|\.)\w+)*\@[A-Za-z\d]+((-|\.)[A-Za-z\d]+)*\.[A-Za-z\d]+$/x";

        $firstNamePatter = "/^[a-z-]+$/i";

        if ( !preg_match( $firstNamePatter, $_POST["firstName"] ) ) $errorMessages[] = "Please enter a valid name";

        if ( !preg_match( $emailPattern, $_POST["email"] ) ) $errorMessages[] = "Please enter a valid Email Address";

        $phone = $_POST['phoneNumber'];
        if(empty( $_POST['phoneNumber'] ) || !preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
            $errorMessages[] = "Please enter your phone number below";
        }

        $website = $_POST['website'];
        if(!filter_var($website, FILTER_VALIDATE_URL)) {
            $siteVerified = $website;
            //$errorMessages[] = "Please enter a valid URL";
        }

        if ( empty( $_POST['message'] ) ) $errorMessages[] = "Please enter your message below";

        if ( empty($_SESSION['captcha'] )
            || strcasecmp($_SESSION['captcha'],
                $_POST['captcha'] ) != 0 ) {
            /*
             * Note: the captcha code is compared case insensitively.
             * if you want case sensitive match, update the check above to
             * strcmp()
             */

            $errorMessages[] = "Please enter a captcha";
        }

        if ( $errorMessages ) {
//            echo "<p>there was a problem with the form you sent:</p>";
//            echo "<ul>";
//            foreach ( $errorMessages as $errorMessage ) echo "<li class='error'>$errorMessage</li>";
//            echo "</ul>";
            displayForm( array() );
        } else {
            //$to = get_option('admin_email');
            $to = "snolan760@gmail.com";
            $from = $_POST['email'];
            $firstName = $_POST['firstName'];
            $phoneNumber = $_POST['phoneNumber'];
            $message = $_POST['message'];
            $url = ($siteVerified) ? " and website is, " . $siteVerified : "";
            $subject = 'Email from Captcha Contact Form';
            $message = $firstName . " has sent you a message, \r\n" .
                htmlspecialchars( $message ) .
                " \r\n contact " . $firstName." by phone " . $phoneNumber . "\r\n" .$url;
            $headers = "From: " . $from . " \r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion();
            $mail = mail($to, $subject, $message, $headers);

            if ( $mail > 0 ) {
                displayThanks();
                unset($_SESSION['captcha']);
            }
        }
    }

    function displayThanks() {
        ?>
        <h2>Thank you for your time <?php echo $_POST['firstName']; ?>, we will get back shortly.</h2>
        <?php
    }

    ?>

</div>

<style type="text/css">
    .cffWrapper {
        margin: 5px !important;
    }

    .cffWrapper label {
        display: block !important;
        float: left !important;
        clear: both !important;
        text-align: left !important;
        margin: 5px !important;
        width: 20% !important;
        font-size: 89% !important;
    }

    .cffWrapper input, select, textarea {
        float: left !important;
        margin: 1em 0 0 0 !important;
        width: 57% !important;
    }

    .cffWrapper input[type=radio], input[type=checkbox], input[type=submit], input[type=reset], input[type=button], input[type=image] {
        width: auto !important;
    }

    .cffWrapper button, input, textarea {
        border: 1px solid #ccc;
        border-radius: 3px;
        font-family: inherit;
        padding: 6px;
        padding: 0.428571429rem;
        border: 1px solid #666 !important;
    }

    .error {
        background: #d33 !important;
        color: white !important;
        margin: 5px !important;
        padding: 2px !important;
        text-align: center !important;
        width: 90% !important;
    }

    .cffWrapper #submitButton {
        padding: 6px 10px !important;
        font-size: 11px !important;
        line-height: 1.428571429 !important;
        font-weight: normal !important;
        color: #7c7c7c !important;
        background-color: #e6e6e6 !important;
        background-repeat: repeat-x !important;
        background-image: -moz-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: -ms-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: -webkit-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: -o-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        border: 1px solid #d2d2d2 !important;
        border-radius: 3px !important;
        box-shadow: 0 1px 2px rgba(64, 64, 64, 0.1) !important;
        cursor: pointer !important;
        margin: 15px !important;
    }

    .cffWrapper #resetButton {
        padding: 6px 10px !important;
        font-size: 11px !important;
        line-height: 1.428571429 !important;
        font-weight: normal !important;
        color: #7c7c7c !important;
        background-color: #e6e6e6 !important;
        background-repeat: repeat-x !important;
        background-image: -moz-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: -ms-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: -webkit-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: -o-linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        background-image: linear-gradient(top, #f4f4f4, #e6e6e6) !important;
        border: 1px solid #d2d2d2 !important;
        border-radius: 3px !important;
        box-shadow: 0 1px 2px rgba(64, 64, 64, 0.1) !important;
        cursor: pointer !important;
        margin: 15px !important;
    }

    .clr { clear: both !important; }
</style>