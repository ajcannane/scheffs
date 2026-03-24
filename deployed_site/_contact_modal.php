<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h2 class="modal-title" id="contactModalLabel">Get in Touch</h2>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<form id="contact-form" method="post" action="contact.php" role="form">
					<div class="messages"></div>

					<div class="controls">
						<div class="field">
							<div class="field half first form-group">
								<label for="form_name">Firstname *</label>
								<input id="form_name" type="text" name="name" class="form-control" placeholder="Please enter your firstname *" required="required"
								    data-error="Firstname is required.">
								<div class="help-block with-errors"></div>
							</div>
							<div class="field half form-group">
								<label for="form_lastname">Lastname *</label>
								<input id="form_lastname" type="text" name="surname" class="form-control" placeholder="Please enter your lastname *" required="required"
								    data-error="Lastname is required.">
								<div class="help-block with-errors"></div>
							</div>
						</div>
						<div class="field half first form-group">
							<label for="form_email">Email *</label>
							<input id="form_email" type="email" name="email" class="form-control" placeholder="Please enter your email *" required="required"
							    data-error="Valid email is required.">
							<div class="help-block with-errors"></div>
						</div>
						<div class="field half form-group">
							<label for="form_phone">Phone</label>
							<input id="form_phone" type="tel" name="phone" class="form-control" placeholder="Please enter your phone">
							<div class="help-block with-errors"></div>
						</div>
						<div class="field form-group">
							<label for="form_message">Message *</label>
							<textarea id="form_message" name="message" class="form-control" placeholder="Message for me *" rows="5" required="required"
							    data-error="Please,leave us a message."></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="field form-group">
							<div id="recaptcha-widget" data-sitekey="<?= htmlspecialchars(getenv('RECAPTCHA_SITE_KEY')) ?>"></div>
						</div>

						<div class="field actions">
							<input type="submit" class="alt" value="Send message">
						</div>
					</div>
					<div class="field">
						<p class="text-muted"><strong>*</strong> These fields are required.</p>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel">
	<div class="modal-dialog modal-dialog-sm" role="document">
		<div class="modal-content modal-content-success">
			<div class="modal-header modal-header-success">
				<h2 class="modal-title" id="successModalLabel">
					<i class="fa fa-check-circle" aria-hidden="true"></i> Message Sent
				</h2>
				<button type="button" class="close close-success" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
				<p>Thank you for getting in touch! We'll get back to you as soon as possible.</p>
			</div>
			<div class="modal-footer-actions">
				<button type="button" class="button" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
