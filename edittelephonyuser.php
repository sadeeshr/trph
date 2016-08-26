<?php

	###########################################################
	### Name: edittelephonyuser.php 						###
	### Functions: Edit Users 								###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016			###
	### Version: 4.0 										###
	### Written by: Alexander Abenoja & Noel Umandap		###
	### License: AGPLv2										###
	###########################################################

	require_once('./php/CRMDefaults.php');
	require_once('./php/UIHandler.php');
	//require_once('./php/DbHandler.php');
	require_once('./php/LanguageHandler.php');
	require('./php/Session.php');
	require_once('./php/goCRMAPISettings.php');

	// initialize structures
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

$userid = NULL;
if (isset($_POST["userid"])) {
	$userid = $_POST["userid"];
}

if(isset($_POST["role"])){
	$userrole = $_POST["role"];
}

$voicemails = $ui->API_goGetVoiceMails();
$user_groups = $ui->API_goGetUserGroupsList();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Users</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>
        <?php print $ui->creamyThemeCSS(); ?>

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
            	<div>
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("Users"); ?>
                        <small><?php $lh->translateText("Edit Users"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if(isset($_POST["userid"])){
						?>	
							<li><a href="./telephonyusers.php"><?php $lh->translateText("Users"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <?php

                	if($userrole == "9"){
					echo "<br/><br/>";
					print $ui->getUnauthotizedAccessMessage();

					}else{
                ?>

               <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
					<!-- standard custom edition form -->
					<?php
					$userobj = NULL;
					$errormessage = NULL;

					$output = $ui->goGetUserInfo($userid, $userrole);
					//var_dump($output);

						if(isset($userid)) {
							if ($output->result=="success") {
							# Result was OK!
							?>

							<div class="panel-body">
							<legend>MODIFY USER : <u><?php echo $output->data->user;?></u></legend>

								<form id="modifyuser">

								<!-- Custom Tabs -->
								<div role="tabpanel">
								<!--<div class="nav-tabs-custom">-->
									<ul role="tablist" class="nav nav-tabs nav-justified">
										<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
										<li><a href="#tab_2" data-toggle="tab"> Advanced Settings</a></li>
									</ul>
					               <!-- Tab panes-->
					               <div class="tab-content">

						               	<!-- BASIC SETTINGS -->
						                <div id="tab_1" class="tab-pane fade in active">

											<input type="hidden" name="modifyid" value="<?php echo $userid;?>" />
										
											<fieldset>
												<div class="form-group mt">
													<label for="fullname" class="col-sm-2 control-label">Fullname</label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="fullname" id="fullname" 
															value="<?php echo $output->data->full_name;?>" maxlength="50" placeholder="Fullname" />
													</div>
												</div>
												<div class="form-group">
													<label for="email" class="col-sm-2 control-label">Email</label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="email" id="email" 
															value="<?php echo $output->data->email;?>"  maxlength="100" placeholder="Email" />
														<small><span id="email_check"></span></small>
													</div>
												</div>
												<div class="form-group">
													<label for="usergroup" class="col-sm-2 control-label">User Group</label>
													<div class="col-sm-10 mb">
														<select class="form-control" id="usergroup" name="usergroup">
															<?php
																for($a=0;$a<count($user_groups->user_group);$a++){
															?>
																<option value="<?php echo $user_groups->user_group[$a];?>" 
																		<?php if($output->data->user_group == $user_groups->user_group[$a]){echo "selected";}?> />  
																	<?php echo $user_groups->user_group[$a].' - '.$user_groups->group_name[$a];?>  
																</option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="status" class="col-sm-2 control-label">Status</label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="status" id="status">
														<?php
															$status = NULL;
															if($output->data->active == "Y"){
																$status .= '<option value="Y" selected> Active </option>';
															}else{
																$status .= '<option value="Y" > Active </option>';
															}
															
															if($output->data->active == "N" || $output->data->active == NULL){
																$status .= '<option value="N" selected> Inactive </option>';
															}else{
																$status .= '<option value="N" > Inactive </option>';
															}
															echo $status;
														?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="userlevel" class="col-sm-2 control-label">User Level</label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="userlevel" id="userlevel">
														<?php
															$userlevel = NULL;
																if($output->data->user_level == "1"){
																	$userlevel .= '<option value="1" selected> 1 </option>';
																}else{
																	$userlevel .= '<option value="1" > 1 </option>';
																}
																if($output->data->user_level == "2"){
																	$userlevel .= '<option value="2" selected> 2 </option>';
																}else{
																	$userlevel .= '<option value="2" > 2 </option>';
																}
																if($output->data->user_level == "3"){
																	$userlevel .= '<option value="3" selected> 3 </option>';
																}else{
																	$userlevel .= '<option value="3" > 3 </option>';
																}
																if($output->data->user_level == "4"){
																	$userlevel .= '<option value="4" selected> 4 </option>';
																}else{
																	$userlevel .= '<option value="4" > 4 </option>';
																}
																if($output->data->user_level == "5"){
																	$userlevel .= '<option value="5" selected> 5 </option>';
																}else{
																	$userlevel .= '<option value="5" > 5 </option>';
																}
																if($output->data->user_level == "6"){
																	$userlevel .= '<option value="6" selected> 6 </option>';
																}else{
																	$userlevel .= '<option value="6" > 6 </option>';
																}
																if($output->data->user_level == "7"){
																	$userlevel .= '<option value="7" selected> 7 </option>';
																}else{
																	$userlevel .= '<option value="7" > 7 </option>';
																}
																if($output->data->user_level == "8"){
																	$userlevel .= '<option value="8" selected> 8 </option>';
																}else{
																	$userlevel .= '<option value="8" > 8 </option>';
																}
																if($output->data->user_level == "9"){
																	$userlevel .= '<option value="9" selected> 9 </option>';
																}else{
																	$userlevel .= '<option value="9" > 9 </option>';
																}
															echo $userlevel;
														?>
															
														</select>
													</div>
												</div>
											</fieldset>
											<fieldset>
												<div class="form-group">
													<label for="phone_login" class="col-sm-2 control-label">Phone Login</label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="phone_login" id="phone_login" 
															value="<?php echo $output->data->phone_login;?>" maxlength="20" placeholder="Phone Login" />
														<label id="phone_login-error"></label>
													</div>
												</div>
												<div class="form-group">
													<label for="phone_password" class="col-sm-2 control-label">Phone Password</label>
													<div class="col-sm-10 mb">
														<input type="text" class="form-control" name="phone_password" id="phone_password" 
															value="<?php echo $output->data->phone_pass;?>" maxlength="20" placeholder="Phone Password" />
													</div>
												</div>									
												<div class="form-group">
													<label for="voicemail" class="col-sm-2 control-label">Voicemail</label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="voicemail" id="voicemail">
															<?php
																if($voicemails == NULL){
															?>
																<option value="" selected>--No Voicemails Available--</option>
															<?php
																}else{
																for($a=0;$a<count($voicemails->voicemail_id);$a++){
															?>
																	<option value="<?php echo $voicemails->voicemail_id;?>" 
																			<?php if($output->data->voicemail_id == $voicemails->voicemail_id[$a]){echo "selected";}?> />
																		<?php echo $voicemails->voicemail_id[$a].' - '.$voicemails->fullname[$a];?>
																	</option>									
															<?php
																	}
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="change_pass" class="col-sm-2 control-label">Change Password?</label>
													<div class="col-sm-10 mb">
														<select class="form-control " name="change_pass" id="change_pass">
															<option value="N" selected> No </option>
															<option value="Y" > Yes </option>
														</select>
													</div>
												</div>
												<div class="form-group form_password" style="display:none;">
													<label for="password" class="col-sm-2 control-label">Password</label>
													<div class="col-sm-10 mb">
														<input type="password" class="form-control" name="password" id="password" 
															value="<?php echo $output->data->password;?>" maxlength="20" placeholder="Password" />
														<small><i><span id="pass_result"></span></i></small>
													</div>
												</div>
												<div class="form-group form_password" style="display:none;">		
													<label for="conf_password" class="col-sm-2 control-label">Confirm Password: </label>
													<div class="col-sm-10 mb">
														<input type="password" class="form-control" id="conf_password" placeholder="Confirm Password" required />
														<span id="pass_result"></span></i></small>
													</div> 
												</div>
											</fieldset>
									   	</div><!-- tab 1 -->

									   	<!-- ADVANCED SETTINGS -->
									   	<div id="tab_2" class="tab-pane fade in">
							       			<input type="hidden" name="agent_choose_ingroup" value="0">
							       			<input type="hidden" name="agent_choose_blended" value="0">
							       			<input type="hidden" name="scheduled_callbacks" value="1">
							       			<input type="hidden" name="agent_call_manual" value="1">

							       			<fieldset>
							       				<div class="form-group mt">
													<label for="hotkeys" class="col-sm-2 control-label">HotKeys</label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="hotkeys" id="hotkeys">
														<?php
															$status = NULL;
															if($output->data->hot_keys == "0"){
																$status .= '<option value="Y" selected> Active </option>';
															}else{
																$status .= '<option value="Y" > Active </option>';
															}
															
															if($output->data->hot_keys == "1" || $output->data->hot_keys == NULL){
																$status .= '<option value="N" selected> Inactive </option>';
															}else{
																$status .= '<option value="N" > Inactive </option>';
															}
															echo $status;
														?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="agent_recordings" class="col-sm-2 control-label">Agent Recordings</label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="agent_recordings" id="agent_recordings">
															<option value="0"> 0 </option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="agent_transfers" class="col-sm-2 control-label">Agent Transfers</label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="agent_transfers" id="agent_transfers">
															<option value="1"> 1 </option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="closer_default_blended" class="col-sm-2 control-label">Closer Default Blended</label>
													<div class="col-sm-10 mb">
														<select class="form-control" name="closer_default_blended" id="closer_default_blended">
															<option value="1"> 1 </option>
														</select>
													</div>
												</div>
											</fieldset>		
										</div>
										
									   	<!-- FOOTER BUTTONS -->
									   	<div id="modifyUSERresult"></div>

									   	<fieldset class="footer-buttons">
					                        <div class="box-footer">
					                           <div class="col-sm-3 pull-right">
														<a href="telephonyusers.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
					                           	
					                                	<button type="submit" class="btn btn-primary" id="modifyUserOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
													
					                           </div>
					                        </div>
					                    </fieldset>

								   		</div>
					            	</div><!-- end of tab content -->
					       		</form>
		                    	</div><!-- tab panel -->

							<?php
								
							} else {
							# An error occured
								echo $output->result;
							}
	                	}

					?>
					</div><!-- body -->

                </section>
				<!-- /.content -->

				<?php
					}
				?>
				</div>
            </aside><!-- /.right-side -->
			
            
			
        </div><!-- ./wrapper -->

        <?php print $ui->standardizedThemeJS(); ?>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

<script type="text/javascript">
	$(document).ready(function() {

		// for cancelling
		$(document).on('click', '#cancel', function(){
			swal("Cancelled", "No action has been done :)", "error");
		});

		/*********
		** validations
		*********/
			$('#change_pass').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "Y") 
				  $('.form_password').show();
				
				if(this.value == "N") 
				  $('.form_password').hide();
				
			});

			// password
			$("#password").keyup(checkPasswordMatch);
			$("#conf_password").keyup(checkPasswordMatch);

			// phone login
			$("#phone_login").keyup(function() {
				clearTimeout($.data(this, 'timer'));
				var wait = setTimeout(validate_user, 500);
				$(this).data('timer', wait);
			});

	// ------------------------

		/******* 
		** EDIT FUNCTION
	 	*******/
			
		$('#modifyUserOkButton').click(function(){ // on click submit
				
			$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
			$('#modifyUserOkButton').prop("disabled", true);

			// variables for check password
			var validate_password = 0;
			var change_pass = document.getElementById('change_pass').value;
			var password = document.getElementById('password').value;
			var conf_password = document.getElementById('conf_password').value;
			
			// variables for check valid email
			var validate_email = 0;
			var email = document.getElementById('email').value;
            var x = document.forms["modifyuser"]["email"].value;
            var atpos = x.indexOf("@");
            var dotpos = x.lastIndexOf(".");

            	// conditional statements
				if(change_pass == "Y"){
					if(password != conf_password){
						validate_password = 1;
					}
					if(password == ""){
						validate_password = 2;
					}
				}
				
                if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
                    validate_email = 1;
                }else{
                	validate_email = 0;
                }

                if(email == ""){
            		validate_email = 0;
            	}

            		// validate results
	                if(validate_email == 1){
	                	$('#update_button').html("<i class='fa fa-check'></i> Update");
						$('#modifyUserOkButton').prop("disabled", false);	
						$("#email_check").html("<font color='red'>Input a Valid Email Address</font>");
						$('#email_check').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
	                }
	                if(validate_password == 1){
	                	$('#update_button').html("<i class='fa fa-check'></i> Update");
						$('#modifyUserOkButton').prop("disabled", false);	
	                }
	                if(validate_password == 2){
	                	$("#pass_result").html("<font color='red'><i class='fa fa-warning'></i> Input and Confirm Password, otherwise mark Change Password? as NO! </font>");
	                	$('#update_button').html("<i class='fa fa-check'></i> Update");
						$('#modifyUserOkButton').prop("disabled", false);	
	                }

	                // validations
	                if(validate_email == 0 && validate_password == 0){
	                	$.ajax({
	                        url: "./php/ModifyTelephonyUser.php",
	                        type: 'POST',
	                        data: $("#modifyuser").serialize(),
	                        success: function(data) {
	                          // console.log(data);
	                            if (data == 1) {
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyUserOkButton').prop("disabled", false);
									swal(
										{
											title: "Success",
											text: "User Successfully Updated!",
											type: "success"
										},
										function(){
											location.replace("./telephonyusers.php");
										}
									);
								} else {
									sweetAlert("Oops...", "Something went wrong! " + data, "error");
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyUserOkButton').prop("disabled", false);	
								}
	                        }
	                    });
					}
				return false;
			});

		// disable special characters and allow spaces on full name
		$('#fullname').bind('keypress', function (event) {
		    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
		    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		    if (!regex.test(key)) {
		       event.preventDefault();
		       return false;
		    }
		});

		// allow only numbers in phone_login
		$('#phone_login').bind('keypress', function (event) {
		    var regex = new RegExp("^[0-9]+$");
		    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
		    if (!regex.test(key)) {
		       event.preventDefault();
		       return false;
		    }
		});
	});

	/*********
	** phone_login validations
	*********/
		function validate_user(){
			var user_form_value = "";
			var phone_logins_value = $('#phone_login').val();
	        if(phone_logins_value != ""){
			    $.ajax({
				    url: "php/checkUser.php",
				    type: 'POST',
				    data: {
				    	user : user_form_value,
				    	phone_login : phone_logins_value
				    },
					success: function(data) {
						console.log(data);
						if(data == "success"){
							checker = 0;
							$( "#phone_login" ).removeClass("error");
							$( "#phone_login-error" ).text( "Phone Login is available." ).removeClass("error").addClass("avail");
							$('#modifyUserOkButton').prop("disabled", false);
						}else{
							$( "#phone_login" ).addClass( "error" );
							$( "#phone_login-error" ).text( data ).removeClass("avail").addClass("error");
							$('#modifyUserOkButton').prop("disabled", true);
							
							checker = 1;
						}
					}
				});
			}
		}

	/**************
	** password validation
	**************/
		function checkPasswordMatch() {
		    var password = $("#password").val();
		    var confirmPassword = $("#conf_password").val();

		    if (password != confirmPassword)
		        $("#pass_result").html("<font color='red'>Passwords Do Not Match! <font size='5'>✖</font> </font>");
		    else
		    	 $("#pass_result").html("<font color='green'>Passwords Match! <font size='5'>✔</font> </font>");
		}
</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
