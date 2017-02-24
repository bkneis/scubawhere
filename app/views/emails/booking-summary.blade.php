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
											Lead Customer<br />
											<strong>
												{{{$booking->lead_customer->firstname}}} {{{$booking->lead_customer->lastname}}}
											</strong>
											<br />
											@if($booking->lead_customer->phone)
												Telephone : <br />
												{{{$booking->lead_customer->phone}}}<br />
											@endif
										</td>
									</tr>
								</table>
							</td>
							<td class="mini-container-right mini-block-padding">
								<table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate !important;">
									<tr>
										<td class="mini-block">
											Booking Reference<br />
											<strong>{{$booking->reference}}</strong><br />
											<br />
											Booking Date<br />
											<strong>{{friendlyDateNoTime($booking->created_at_local)}}</strong>
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
											<td style="padding-bottom: 10px !important;" class="title-dark">
												 Trips &amp; Classes
											</td>
										</tr>

										@foreach($booking->bookingdetails as $detail)
											<tr>
												<td class="item-col" style="padding-bottom: 20px;">
													<table cellspacing="0" cellpadding="0" width="100%">
														<tr>
															<td class="item-col-inner title">
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

																<span style="color: #4d4d4d; font-size: 14px; display: block; margin-top: 5px; margin-bottom: -15px; padding-bottom: 10px !important;">
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
																		<td style="width: 90px; padding-bottom: 10px !important;">
																			<span style="color: #4d4d4d; font-weight:bold;">Customer:</span>
																		</td>
																		<td>
																			{{{$detail->customer->firstname}}} {{{$detail->customer->lastname}}}
																		</td>
																	</tr>

																	@if(!empty($detail->ticket))
																		<tr>
																			<td style="padding-bottom: 10px !important;">
																				<span style="color: #4d4d4d; font-weight:bold;">Ticket:</span>
																			</td>
																			<td>
																				{{{$detail->ticket->name}}}
																			</td>
																		</tr>
																	@endif

																	@if(!empty($detail->training))
																		<tr>
																			<td style="padding-bottom: 10px !important;">
																				<span style="color: #4d4d4d; font-weight:bold;">Class:</span>
																			</td>
																			<td>
																				{{{$detail->training->name}}}
																			</td>
																		</tr>
																	@endif
																	@if(!$detail->addons->isEmpty())
																		<tr>
																			<td style="width: 90px; padding-bottom: 10px !important;">
																				<span style="color: #4d4d4d; font-weight:bold;">Addons:</span>
																			</td>
																			<td>
																				@foreach($detail->addons as $addon)
																					{{{$addon->name}}} <small>x <span class="badge badge-default">{{$addon->pivot->quantity}}</span></small><br />
																				@endforeach
																			</td>
																		</tr>
																	@endif

																	@if(!empty($detail->course))
																		<tr>
																			<td style="padding-bottom: 10px !important; width: 90px;">
																				<span style="color: #4d4d4d; font-weight:bold;">Course:</span>
																			</td>
																			<td style="padding-bottom: 0;">
																				<i class="fa fa-graduation-cap fa-fw"></i> {{{$detail->course->name}}}
																			</td>
																		</tr>
																	@endif

																	@if(!empty($detail->packagefacade))
																		<tr>
																			<td style="padding-top: 10px !important; width: 90px;">
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
										</tr>

										@foreach($booking->accommodations as $accommodation)
											<tr>
												<td class="item-col item">
													<table cellspacing="0" cellpadding="0" width="100%">
														<tr>
															<td class="item-col-inner title">
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
	<?php echo("<table>"); ?>
@stop
