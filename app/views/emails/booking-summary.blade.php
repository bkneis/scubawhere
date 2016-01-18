@extends('emails.layouts.main')

@section('title')
	<title>{{$company->name}} Booking Summary</title>
@stop

@section('content')

	<?php
		function isObjectEmpty($obj)
		{
			foreach($obj as $x)
				return false;

			return true;
		}

		function friendlyDate($date)
		{
			return date('d M Y H:i', strtotime($date));
		}

		function friendlyDateNoTime($date)
		{
			return date('d M Y', strtotime($date));
		}

		function tripFinish($start, $duration)
		{
			$startDate = friendlyDate($start);

			$endDate          = new DateTime($start);
			$duration_hours   = floor($duration);
			$duration_minutes = round( ($duration - $duration_hours) * 60 );
			$endDate->add( new DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M') );
			$endDate = $endDate->format('Y-m-d H:i:s');

			$endDate = friendlyDate($endDate);

			if(substr($startDate, 0, 11) === substr($endDate, 0, 11))
				// Only return the time, if the date is the same
				return substr($endDate, 12);
			else
				// Only return the date and the Month (and time)
				return substr($endDate, 0, 6) . ' ' . substr($endDate, 12);
		}
	?>
	<table align="center" cellpadding="0" cellspacing="0" class="container-for-gmail-android" width="100%" style="max-width: 800px;">
		<tr>
			<td align="center" valign="top" width="100%" class="content-padding">
				<center>
					<h1 align="center">{{$company->name}} Booking Summary</h1>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td class="mini-container-left mini-block-padding">
								<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate !important;">
									<tr>
										<td class="mini-block">
											<span class="header-sm">Lead Customer</span><br />
											{{{$booking->lead_customer->firstname}}} {{{$booking->lead_customer->lastname}}}<br />
											@if($booking->lead_customer->address_1)
												{{{$booking->lead_customer->address_1}}}<br />
											@endif
											@if($booking->lead_customer->address_2)
												{{{$booking->lead_customer->address_2}}}<br />
											@endif
											@if($booking->lead_customer->city)
												{{$booking->lead_customer->city}},
											@endif
											@if($booking->lead_customer->county)
												{{$booking->lead_customer->county}},
											@endif
											@if($booking->lead_customer->postcode)
												{{$booking->lead_customer->postcode}}
											@endif
											<br />
											{{{$booking->lead_customer->country->name}}}
										</td>
									</tr>
								</table>
							</td>
							<td class="mini-container-right mini-block-padding">
								<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate !important;">
									<tr>
										<td class="mini-block">
											Booking Reference<br />
											<span class="header-sm">{{$booking->reference}}</span><br />
											<br />
											Booking Date<br />
											<span class="header-sm">{{friendlyDateNoTime($booking->created_at_local)}}</span>
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
			<td valign="top" width="100%" style="background-color: #ffffff; border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;">
				<center>
					<table cellpadding="0" cellspacing="0" width="600" class="w320">
						<tr>
							<td class="item-table">
								<table cellspacing="0" cellpadding="0" width="100%">

									@if(!isObjectEmpty($booking->bookingdetails))
										<tr>
											<td class="title-dark">
												 Trips & Classes
											</td>
											<td class="title-dark" width="100"></td>
											<td class="title-dark" width="100"></td>
										</tr>

										@foreach($booking->bookingdetails as $detail)
											<tr>
												<td class="item-col" colspan="3">
													<table cellspacing="0" cellpadding="0" width="100%">
														<tr>
															<td class="item-col-inner title" colspan="2">
																<span style="color: #4d4d4d; font-weight:bold; font-size: 17px;">
																	@if(!empty($detail->ticket))
																		<i class="fa fa-ship fa-fw"></i>
																		@if($detail->temporary)
																			-
																		@else
																			{{{$detail->session->trip->name}}}
																		@endif
																	@else
																		<i class="fa fa-graduation-cap fa-fw"></i>
																		@if($detail->temporary)
																			-
																		@else
																			{{{$detail->training->name}}}
																		@endif
																	@endif
																</span>

																<span style="color: #4d4d4d; font-size: 14px; display: block; margin-top: 5px; margin-left: 28px; margin-bottom: -15px;">
																	@if($detail->temporary)
																		No date set
																	@else
																		@if(!empty($detail->session))
																			{{friendlyDate($detail->session->start)}} -
																			{{tripFinish($detail->session->start, $detail->session->trip->duration)}}
																		@else
																			{{friendlyDate($detail->training_session->start)}} -
																			{{tripFinish($detail->training_session->start, $detail->training->duration)}}
																		@endif
																	@endif
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
																			{{{$detail->customer->firstname}}} {{{$detail->customer->lastname}}}
																		</td>
																	</tr>

																	@if(!empty($detail->ticket))
																		<tr>
																			<td>
																				<span style="color: #4d4d4d; font-weight:bold;">Ticket:</span>
																			</td>
																			<td>
																				{{{$detail->ticket->name}}}
																			</td>
																		</tr>
																	@endif

																	@if(!empty($detail->training))
																		<tr>
																			<td>
																				<span style="color: #4d4d4d; font-weight:bold;">Class:</span>
																			</td>
																			<td>
																				{{{$detail->training->name}}}
																			</td>
																		</tr>
																	@endif
																</table>
															</td>
															<td class="item-col-inner item">
																<table cellspacing="0" cellpadding="0" width="100%">
																	@if(!empty($detail->addons))
																		<tr>
																			<td style="width: 90px;">
																				<span style="color: #4d4d4d; font-weight:bold;">Addons:</span>
																			</td>
																			<td>
																				@foreach($detail->addons as $addon)
																					{{{$addon->name}}} <small><span class="badge badge-default">{{$addon->pivot->quantity}}</span></small><br />
																				@endforeach
																			</td>
																		</tr>
																	@endif

																	@if(!empty($detail->course))
																		<tr>
																			<td style="padding-bottom: 0; width: 90px;">
																				<span style="color: #4d4d4d; font-weight:bold;">Course:</span>
																			</td>
																			<td style="padding-bottom: 0;">
																				<i class="fa fa-graduation-cap fa-fw"></i> {{{$detail->course->name}}}
																			</td>
																		</tr>
																	@endif

																	@if(!empty($detail->packagefacade))
																		<tr>
																			<td style="padding-top: 0; width: 90px;">
																				<span style="color: #4d4d4d; font-weight:bold;">Package:</span>
																			</td>
																			<td style="padding-top: 0;">
																				<i class="fa fa-tags fa-fw"></i> {{{$detail->packagefacade->package->name}}}
																			</td>
																		</tr>
																	@endif
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										@endforeach
									@endif

									@if(!isObjectEmpty($booking->accommodations))
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

										@foreach($booking->accommodations as $accommodation)
											<tr>
												<td class="item-col item" colspan="3">
													<table cellspacing="0" cellpadding="0" width="100%">
														<tr>
															<td class="item-col-inner title" colspan="2">
																<span style="color: #4d4d4d; font-weight:bold; font-size: 17px;">
																	<i class="fa fa-bed fa-fw"></i> {{{$accommodation->name}}}
																</span>

																<span style="color: #4d4d4d; font-size: 14px; display: block; margin-top: 5px; margin-left: 28px; margin-bottom: -15px;">
																	{{friendlyDateNoTime($accommodation->pivot->start)}} - {{friendlyDateNoTime($accommodation->pivot->end)}}
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
																			{{{$accommodation->customer->firstname}}} {{{$accommodation->customer->lastname}}}
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										@endforeach
									@endif
								</table>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	<?php echo("<table>"); /*

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
											<td style="vertical-align: middle; border-right: 1px solid #ccc;">
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
		{{/compare}}
	</table>
	*/ ?>
@stop
