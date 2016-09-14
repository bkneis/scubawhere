<?php namespace ScubaWhere\Services;

use ScubaWhere\Context;
use ScubaWhere\Repositories\CreditRepoInterface;

/**
 * Class: CreditsService
 *
 * The service layer to managing a user (Dive Operator's) credit usage with the RMS, it is responsible
 * for managing the booking and email credits assigned to users on licence activation.
 *
 */
class CreditService {

	protected $credits_repo;
	protected $company_model;

	/**
	 * Constructor to assign the company_model, this acts as the root in which all data
	 * must come through via realtions to the company. It also utilizes the IoC container
	 * to inject the CreditRepo as a dependency.
	 *
	 * @param CreditRepoInterface $credit_repo
	 */
	public function __construct(CreditRepoInterface $credit_repo)
	{
		$this->credit_repo = $credit_repo;
		$this->company_model = Context::get();
	}

	/**
	 * Returns a companies credit usage for their active licence period
	 *
	 * @return string Date their licence expires
	 * @return array  Amount of booking credits used and their total
	 * @return array  Amount of email credits used and their total 
	 */
	public function getCredits()
	{
		$between_dates = $this->getActiveDates();
		$used_bookings = $this->getBookingCredits($between_dates);
		$used_emails   = $this->getEmailCredits($between_dates);
		$credit		   = $this->credit_repo->getAll();

		return array(
			'renewal_date' => $between_dates[1],
			'bookings'	   => array(
				'used'	=> $used_bookings,
				'total'	=> $credit->booking_credits
			),
			'emails'	   => array(
				'used'	=> $used_emails,
				'total'	=> $credit->email_credits
			)
		);
	} 

	/**
	 * @return string Expiry date of a companies licence for the RMS
	 */
	public function getRenewalDate()
	{
		return $this->credit_repo->get('renewal_date');
	}

	/**
	 * Calculates the number of bookings made between a start and end date
	 *
	 * @param array the start and end date of their licence
	 * @return integer Number of bookings made
	 */
	private function getBookingCredits($between_dates)
	{
		return $this->company_model->bookings()
								   ->where('status', '=', 'confirmed')
							 	   ->whereBetween('created_at', $between_dates)
							 	   ->count();
	}

	/**
	 * Calculates the number of emails sent between a start and end date
	 *
	 * @param array the start and end date of their licence
	 * @return integer Number of email sent
	 */
	private function getEmailCredits($between_dates)
	{
		return $this->company_model->campaigns()
							 	   ->whereBetween('created_at', $between_dates) 
							 	   ->sum('num_sent');
	}

	/**
	 * @return array Start and end dates of the companies licence
	 */
	private function getActiveDates()
	{
		$to = new \DateTime($this->getRenewalDate());
		$from = new \DateTime($to->format('Y-m-d H:i:s'));
		$from = $from->sub(new \DateInterval('P1Y'));
		$to = $to->format('Y-m-d H:i:s');
		$from = $from->format('Y-m-d H:i:s');
		return array($from, $to);
	}

}
