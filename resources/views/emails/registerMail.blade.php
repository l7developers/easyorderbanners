<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 *
 */
?>

<div>
	<p><?php echo __('Hi ');?><?php echo $data->fname." ".$data->lname;?></p>
	<p>Thank You for Register our site <b>{{ config('constants.SITE_NAME') }}</b> </p>
	<p>You can login on site {{ config('constants.SITE_NAME') }} by click on below activation link.</p>
	<br/>
	<a href="<?php echo config('constants.SITE_URL')."login"?>" target="_blank"><?php echo __('Login Account');?>!</a><br/>
	<br/>
	<?php echo __('If the above link doesn\'t work for you, try copy-and-pasting the following url into your web browser');?>: 
	<br/><br/>
	<?php echo config('constants.SITE_URL')."login";?>
	<br/>
	<br/>
	
	<p>Regards</p>
	<p>{{ config('constants.ADMIN_NAME') }}</p>
	<p>If you have any concern please feel free to contact at {{ config('constants.ADMIN_MAIL') }}</p>
</div>