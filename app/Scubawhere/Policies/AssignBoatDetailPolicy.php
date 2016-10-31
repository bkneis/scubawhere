<?php

namespace Scubawhere\Policies;

use Scubawhere\Entities\Departure;
use Scubawhere\Exceptions\HTTPForbiddenException;
use Scubawhere\Exceptions\HTTPPreconditionFailed;

class AssignBoatDetailPolicy implements BasePolicy
{
	/**
	 * The ticket model used to check the boat is valid for that trip.
	 *
	 * Tickets can limit certain boats to be used and overnight trips require a cabin to
	 * be available, theirfore we must pass this model and eager load its relationships.
	 *
	 * @var \Ticket
	 */
	protected $ticket;

	/**
	 * The trip the boat will be used for
	 *
	 * @var \Trip
	 */
	protected $trip;

	/**
	 * The session that the boat will be used for.
	 *
	 * @var Departure
	 */
	protected $departure;

	/**
	 * Variable used to assign a valid boatroom id when only one type of cabin is available
	 *
	 * @var int
	 */
	protected $boatroom_id;

	/**
	 * The ID of the boatroom submitted in the API request.
	 *
	 * This variable is needed to check that the cabin the requester wants to submit is the same as 
	 * the validated boatroom ID.
	 *
	 * @var int
	 */
	protected $boatroom_id_req;

	/**
	 * The capacity of the boat, used top ensure it is not already full for the session.
	 *
	 * @var array
	 */
	protected $capacity;

	public function __construct($ticket, $trip, $departure, $boatroom_id_req)
	{
		$this->ticket          = $ticket;
		$this->trip            = $trip;
		$this->departure       = $departure;
		$this->boatroom_id     = false;
		$this->boatroom_id_req = $boatroom_id_req;
		$this->capicity        = null;
	}

	/**
	 * Check that the boat can be used with the ticket
	 *
	 * @throws \Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function isBoatValidForTicket()
	{
		if ($this->ticket->boats()->exists()) {
			$boatIDs = $this->ticket->boats()->lists('id');
			if (!in_array($this->departure->boat_id, $boatIDs)) {
				throw new HTTPForbiddenException(['This ticket is not eligable for this trip\'s boat.']);
			}
		}
	}

	/**
	 * Check that the boat has a cabin available, and their is enough capacity left
	 *
	 * @throws \Scubawhere\Exceptions\HTTPForbiddenException
	 * @throws \Scubawhere\Exceptions\HTTPPreconditionFailed
	 * @throws \Scubawhere\Exceptions\InvalidInputException
	 */
	protected function isCabinValid()
	{
		$boatBoatrooms = $this->departure->boat->boatrooms()->lists('id');
		$ticketBoatrooms = $this->ticket->boatrooms()->lists('id');

		// Check if the session's boat's boatrooms are allowed for the ticket
		if (count($ticketBoatrooms) > 0) {
			$intersect = array_intersect($boatBoatrooms, $ticketBoatrooms);
			if (count($intersect) === 0) {
				throw new HTTPForbiddenException(['This ticket is not eligable for this trip\'s boat\'s cabin(s).']);
			}

			if (count($intersect) === 1) {
				$this->boatroom_id = $intersect[0];
			}
		}

		// Just in case, check if the boat has boatrooms assigned
		if (count($boatBoatrooms) === 0) {
			throw new HTTPPreconditionFailed(['Could not assign the customer, the boat has no cabins.']);
		}

		// Check if the boat only has one boatroom assigned
		if (count($boatBoatrooms) === 1) {
			$this->boatroom_id = $boatBoatrooms[0];
		}

		// Check if the boatroom is still not determined
		if ($this->boatroom_id === false) {
			// Check if a boatroom_id got submitted
			if (is_null($this->boatroom_id_req)) {
				throw new InvalidInputException(['Please select in which cabin the customer will sleep.']);
			}

			// Check if the submitted boatroom_id is allowed
			$this->boatroom_id = $this->boatroom_id_req;
			if (!in_array($this->boatroom_id, $boatBoatrooms) 
				|| (count($ticketBoatrooms) > 0 
				&& !in_array($this->boatroom_id, $ticketBoatrooms))
			) {
				throw new InvalidInputException(['The selected cabin cannot be booked for this session.']);
			}

		} else {
			// The above checks already determined that there is only one possible boatroom to take
			// If a boatroom_id got submitted anyway, check if it is the same that we determined
			if ((!is_null($this->boatroom_id_req)) && $this->boatroom_id_req != $this->boatroom_id) {
				throw new InvalidInputException(['The selected cabin cannot be booked for this session.']);
			}
		}
	}

	/**
	 * Check that both the boat and cabin are not full
	 *
	 * @throws \Scubawhere\Exceptions\HTTPForbiddenException
	 */
	protected function isBoatOrCabinsFull()
	{
		// Validate remaining capacity on session
		$this->capacity = $this->departure->getCapacityAttribute();
		if ($this->capacity[0] >= $this->capacity[1]) {
			// Session/Boat already full/overbooked
			throw new HTTPForbiddenException(['The session is already fully booked!']);
		}

		// If a boatroom is needed, validate remaining capacity of boatroom
		if ($this->boatroom_id !== null 
			&& $this->capacity[2][$this->boatroom_id][0] 
			>= $this->capacity[2][$this->boatroom_id][1]
		) {
			// The selected/required boatroom is already full/overbooked
			throw new HTTPForbiddenException(['The selected cabin is already fully booked!']);
		}
	}

	/**
	 * Determine if the session is an overnight trip
	 *
	 * @param $trip      \Trip
	 * @param $departure Departure
	 *
	 * @return bool
	 */
	private function isTripOvernight()
	{
		// Determine if we need a boatroom_id (only when the trip is overnight)
		$start = new \DateTime($this->departure->start);
		$end = clone $start;
		$duration_hours = floor($this->trip->duration);
		$duration_minutes = round(($this->trip->duration - $duration_hours) * 60);
		$end->add(new \DateInterval('PT'.$duration_hours.'H'.$duration_minutes.'M'));
		return ($start->format('Y-m-d') !== $end->format('Y-m-d'));
	}

	/**
	 * Perform all the validation for the policy
	 */
	public function allows()
	{
		$this->isBoatValidForTicket();
		$is_overnight = $this->isTripOvernight();
		if($is_overnight) {
			$this->isCabinValid();
		}
		else {
			$this->boatroom_id = null;
		}
		$this->isBoatOrCabinsFull();
	}

}

