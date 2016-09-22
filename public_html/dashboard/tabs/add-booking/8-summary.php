<div class="row row-header">
	<div class="col-xs-12">
		<div class="page-header">
			<h2>Summary <small>Booking summary</small></h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12" id="summary-container" style="margin-top: -38px;">
	</div>

	<script type="text/x-handlebars-template" id="summary-template">
		<table align="center" cellpadding="0" cellspacing="0" class="container-for-gmail-android" width="100%" style="max-width: 800px;">
			<tr>
				<td align="center" valign="top" width="100%" class="content-padding">
					<center>
						<table cellspacing="0" cellpadding="0" width="600" class="w320">
							<tr>
								<td class="w320">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td class="mini-container-left">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td class="mini-block-padding">
															<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate !important;">
																<tr>
																	<td class="mini-block">
																		<span class="header-sm">Lead Customer</span><br />
																		{{{lead_customer.firstname}}} {{{lead_customer.lastname}}}<br />
																		{{#if lead_customer.address_1}}{{{lead_customer.address_1}}}<br />{{/if}}
																		{{#if lead_customer.address_2}}{{{lead_customer.address_2}}}<br />{{/if}}
																		{{#if lead_customer.city}}{{lead_customer.city}},{{/if}}
																		{{#if lead_customer.county}}{{lead_customer.county}},{{/if}}
																		{{#if lead_customer.postcode}}{{lead_customer.postcode}}{{/if}}<br />
																		{{{lead_customer.country.name}}}
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
											<td class="mini-container-right">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td class="mini-block-padding">
															<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate !important;">
																<tr>
																	<td class="mini-block">
																		Booking Date<br />
																		<span class="header-sm">{{friendlyDateNoTime created_at_local}}</span><br />
																		<br />
																		Booking Reference<br />
																		<span class="header-sm">{{reference}}</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</center>
				</td>
			</tr>

			<tr>
				<td align="center" valign="top" width="100%" style="background-color: #ffffff; border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;">
					<center>
						<table cellpadding="0" cellspacing="0" width="600" class="w320">
							<tr>
								<td class="item-table">
									<table cellspacing="0" cellpadding="0" width="100%">

										{{#if bookingdetails}}
											<tr>
												<td class="title-dark">
													 Trips & Classes
												</td>
												<td class="title-dark" width="100"></td>
												<td class="title-dark" width="100"></td>
											</tr>

											{{#each bookingdetails}}
												<tr>
													<td class="item-col" colspan="3">
														<table cellspacing="0" cellpadding="0" width="100%">
															<tr>
																<td class="item-col-inner title" colspan="2">
																	<span style="color: #4d4d4d; font-weight:bold; font-size: 17px;">
																		{{#if ticket}}
																			<i class="fa fa-ship fa-fw"></i> {{#if temporary}}-{{else}}{{{session.trip.name}}}{{/if}}
																		{{else}}
																			<i class="fa fa-graduation-cap fa-fw"></i> {{#if temporary}}-{{else}}{{{training.name}}}{{/if}}
																		{{/if}}
																	</span>

																	<span style="color: #4d4d4d; font-size: 14px; display: block; margin-top: 5px; margin-left: 28px; margin-bottom: -15px;">
																		{{#if temporary}}
																			No date set
																		{{else}}
																			{{#if session}}
																				{{friendlyDate session.start}} - {{tripFinish session.start session.trip.duration}}
																			{{else}}
																				{{friendlyDate training_session.start}} - {{tripFinish training_session.start training.duration}}
																			{{/if}}
																		{{/if}}
																	</span>
																</td>
															</tr>
															<tr>
																<td class="item-col-inner item" style="padding-left: 28px;">
																	<table cellspacing="0" cellpadding="0" width="100%">
																		<tr>
																			<td style="width: 90px;">
																				<span style="color: #4d4d4d; font-weight:bold;">Customer:</span>
																			</td>
																			<td>
																				{{{customer.firstname}}} {{{customer.lastname}}}
																			</td>
																		</tr>

																		{{#if ticket}}
																			<tr>
																				<td>
																					<span style="color: #4d4d4d; font-weight:bold;">Ticket:</span>
																				</td>
																				<td>
																					{{{ticket.name}}}
																				</td>
																			</tr>
																		{{/if}}

																		{{#if training}}
																			<tr>
																				<td>
																					<span style="color: #4d4d4d; font-weight:bold;">Class:</span>
																				</td>
																				<td>
																					{{{training.name}}}
																				</td>
																			</tr>
																		{{/if}}
																	</table>
																</td>
																<td class="item-col-inner item">
																	<table cellspacing="0" cellpadding="0" width="100%">
																		{{#if addons}}
																			<tr>
																				<td style="width: 90px;">
																					<span style="color: #4d4d4d; font-weight:bold;">Addons:</span>
																				</td>
																				<td>
																					{{#each addons}}
																						{{{name}}} <small><span class="badge badge-default">{{pivot.quantity}}</span></small><br />
																					{{/each}}
																				</td>
																			</tr>
																		{{/if}}

																		{{#if course}}
																			<tr>
																				<td style="padding-bottom: 0; width: 90px;">
																					<span style="color: #4d4d4d; font-weight:bold;">Course:</span>
																				</td>
																				<td style="padding-bottom: 0;">
																					<i class="fa fa-graduation-cap fa-fw"></i> {{{course.name}}}
																				</td>
																			</tr>
																		{{/if}}

																		{{#if packagefacade}}
																			<tr>
																				<td style="padding-top: 0; width: 90px;">
																					<span style="color: #4d4d4d; font-weight:bold;">Package:</span>
																				</td>
																				<td style="padding-top: 0;">
																					<i class="fa fa-tags fa-fw"></i> {{{packagefacade.package.name}}}
																				</td>
																			</tr>
																		{{/if}}
																	</table>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											{{/each}}
										{{/if}}

										{{#if accommodations}}
											<tr>
												<td class="item-col item mobile-row-padding" style="border-bottom: 0;"></td>
											</tr>

											<tr>
												<td class="title-dark">
													 Accommodations
												</td>
												<td class="title-dark" width="100"></td>
												<td class="title-dark" width="100"></td>
											</tr>

											{{#each accommodations}}
												<tr>
													<td class="item-col item" colspan="3">
														<table cellspacing="0" cellpadding="0" width="100%">
															<tr>
																<td class="item-col-inner title" colspan="2">
																	<span style="color: #4d4d4d; font-weight:bold; font-size: 17px;">
																		<i class="fa fa-bed fa-fw"></i> {{{name}}}
																	</span>

																	<span style="color: #4d4d4d; font-size: 14px; display: block; margin-top: 5px; margin-left: 28px; margin-bottom: -15px;">
																		{{friendlyDateNoTime pivot.start}} - {{friendlyDateNoTime pivot.end}}
																	</span>
																</td>
															</tr>
															<tr>
																<td class="item-col-inner item" style="padding-left: 28px;">
																	<table cellspacing="0" cellpadding="0" width="100%">
																		<tr>
																			<td style="width: 90px;">
																				<span style="color: #4d4d4d; font-weight:bold;">Customer:</span>
																			</td>
																			<td>
																				{{{customer.firstname}}} {{{customer.lastname}}}
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
													</td>
												</tr>
											{{/each}}
										{{/if}}
									</table>
								</td>
							</tr>
						</table>
					</center>
				</td>
			</tr>

			<tr><td>&nbsp;</td><tr>

			<tr>
				<td align="center" valign="top" width="100%" style="background-color: #ffffff;	border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;">
					<center>
						<table cellpadding="0" cellspacing="0" width="600" class="w320">
							<tr>
								<td class="item-table">
									<table cellspacing="0" cellpadding="0" width="100%">
										<tr>
											<td class="title-dark">
												 Price Breakdown
											</td>
											<td class="title-dark" width="100"></td>
											<td class="title-dark" width="100"></td>
										</tr>
										{{#each packagesSummary}}
											<tr>
												<td class="inner-item-col">
													<i class="fa fa-tags fa-fw"></i> {{{name}}}
												</td>
												<td class="inner-item-col" style="text-align: right;">
													x1
												</td>
												<td class="inner-item-col" style="text-align: right; padding-right: 20px;">
													{{currency}} {{decimal_price}}
												</td>
											</tr>
										{{/each}}
										{{#each coursesSummary}}
											<tr>
												<td class="inner-item-col">
													<i class="fa fa-graduation-cap fa-fw"></i> {{{name}}}
												</td>
												<td class="inner-item-col" style="text-align: right;">
													x1
												</td>
												<td class="inner-item-col" style="text-align: right; padding-right: 20px;">
													{{currency}} {{decimal_price}}
												</td>
											</tr>
										{{/each}}
										{{#each ticketsSummary}}
											<tr>
												<td class="inner-item-col">
													<i class="fa fa-ticket fa-fw"></i> {{{name}}}
												</td>
												<td class="inner-item-col" style="text-align: right;">
													x1
												</td>
												<td class="inner-item-col" style="text-align: right; padding-right: 20px;">
													{{currency}} {{decimal_price}}
												</td>
											</tr>
										{{/each}}
										{{#each addonsSummary}}
											<tr>
												<td class="inner-item-col">
													<i class="fa fa-cart-plus fa-fw"></i> {{{name}}}
												</td>
												<td class="inner-item-col" style="text-align: right;">
													x{{qtySummary}}
												</td>
												<td class="inner-item-col" style="text-align: right; padding-right: 20px;">
													{{currency}} {{decimal_price}}
												</td>
											</tr>
										{{/each}}
										{{#each accommodations}}
											{{#unless pivot.packagefacade_id}}
												<tr>
													<td class="inner-item-col">
														<i class="fa fa-bed fa-fw"></i> {{{name}}}
													</td>
													<td class="inner-item-col" style="text-align: right;">
														{{numberOfNights pivot.start pivot.end}}
													</td>
													<td class="inner-item-col" style="text-align: right; padding-right: 20px;">
														{{currency}} {{decimal_price}}
													</td>
												</tr>
											{{/unless}}
										{{/each}}

										<tr>
											<td class="item-col item" style="border-top: 1px solid #cccccc;">
											</td>
											<td class="item-col quantity" style="text-align: right; padding-right: 10px; border-top: 1px solid #cccccc;">
												<span class="total-space">Subtotal</span><br />

												{{#compare discount '!==' '0.00'}}
													<span class="total-space">Discount</span><br />
												{{/compare}}

												{{!-- <span class="total-space">Tax</span><br /> --}}

												{{#if agent_id}}
													<span class="total-space" style="font-weight: bold; color: #4d4d4d">Gross</span><br />
													<span class="total-space">{{commission_percentage agent_id}} Commission</span><br />
													<span class="total-space" style="font-weight: bold; color: #4d4d4d">Net</span>
												{{else}}
													<span class="total-space" style="font-weight: bold; color: #4d4d4d">Total</span>
												{{/if}}
											</td>
											<td class="item-col price" style="text-align: right; border-top: 1px solid #cccccc; padding-right: 20px;">
												<span class="total-space">{{decimal_price_without_discount_applied}}</span><br />

												{{#compare discount '!==' '0.00'}}
													<span class="total-space">-{{discount}}</span><br />
												{{/compare}}

												{{!-- <span class="total-space">$0.75</span><br /> --}}

												{{#if agent_id}}
													<span class="total-space" style="font-weight: bold; color: #4d4d4d;">{{currency}} {{decimal_price}}</span><br />
													<span class="total-space">-{{commission_amount agent_id decimal_price}}</span><br />
													<span style="font-weight: bold; color: #4d4d4d; border-bottom: 1px solid;">{{currency}} {{commission_result agent_id decimal_price}}</span>
												{{else}}
													<span style="font-weight: bold; color: #4d4d4d; border-bottom: 1px solid;">{{currency}} {{decimal_price}}</span>
												{{/if}}
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</center>
				</td>
			</tr>

			<tr><td>&nbsp;</td><tr>

			<tr>
				<td align="center" valign="top" width="100%" style="background-color: #ffffff;	border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;">
					<center>
						<table cellpadding="0" cellspacing="0" width="600" class="w320">
							<tr>
								<td class="item-table">
									<table cellspacing="0" cellpadding="0" width="100%">
										<tr>
											<td class="title-dark">
												 Booking Status
											</td>
											<td class="title-dark" width="100"></td>
											<td class="title-dark" width="100"></td>
										</tr>
										<tr>
											<td class="inner-item-col">
												<h4 id="status">{{statusIcon}}</h4>
												<p>{{sourceIcon}}<p>
											</td>
											<td></td>
											<td></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</center>
				</td>
			</tr>

			{{#compare status '!==' 'temporary'}}
				{{!--{{#if isViewMode}}--}}
				<tr>
					<td align="center" valign="top" width="100%">
						<center>
							<table cellpadding="0" cellspacing="0" width="600" class="w320">
								<tr>
									<td class="item-table">
										<table cellspacing="0" cellpadding="0" width="100%">
											<tr>
												<td class="title-dark" width="50%">
													 Options
												</td>
												<td class="title-dark" width="50%"></td>
											</tr>
											<tr>
												<td id="option-buttons" style="vertical-align: middle; border-right: 1px solid #ccc;">
													<button class="btn btn-success btn-block save-booking mb10"{{saveable}}><i class="fa fa-save fa-fw"></i> Save For Later</button>
													{{#unless price}}
														<button class="btn btn-primary btn-block confirm-booking mb10"><i class="fa fa-check fa-fw"></i> Confirm booking</button>
													{{else}}
														{{#if agent_id}}
															<button class="btn btn-primary btn-block confirm-booking mb10"><i class="fa fa-check fa-fw"></i> Confirm booking</button>
														{{else}}
															<button onclick="addTransaction();" class="btn btn-primary btn-block add-transaction"><i class="fa fa-credit-card fa-fw"></i> Add Transaction</button>
														{{/if}}
													{{/unless}}
												</td>
												<td>
													<h4 class="text-center">Reserve Booking</h4>
													<form id="reserve-booking" class="form-horizontal">
														<div class="form-group">
															<div class="radio col-md-12">
																<label>
																	<input type="radio" name="email" id="email-yes" value="1" checked>
																	Send confirmation email to customer
																</label>
															</div>
															<div class="radio col-md-12">
																<label>
																	<input type="radio" name="email" id="email-no" value="0">
																	Do not send email
																</label>
															</div>
														</div>
														<div class="form-group">
															<label for="reserve-until" class="col-sm-6 control-label">Reserve for (hours)</label>
															<div class="col-md-6">
																<input id="reserve-until" name="reserved_until" class="form-control" type="number" value="24">
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-12">
																<button class="btn btn-warning btn-block"><i class="fa fa-clock-o fa-fw"></i> Reserve</button>
															</div>
														</div>
													</form>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</center>
					</td>
					</tr>
					{{!--{{/if}}--}}
			{{/compare}}
		</table>
	</script>
</div>
