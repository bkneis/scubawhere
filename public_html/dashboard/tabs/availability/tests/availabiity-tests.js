function getFakeAccommodationAvailabilityData() {
    let data = [];
    for(let i = 0; i < 10; i++) {
        data.push({
            bookings : [],
            capacity : faker.random.number(),
            company_id : faker.random.number(),
            created_at : faker.date.past(),
            deletatable : faker.random.boolean(),
            deleted_at : faker.date.past(),
            description : faker.random.sentence(),
            id : faker.random.number(),
            name : faker.random.name(),
            updated_at : faker.date.past()
        });
    }
    return data;
}
describe('Test suite for the availabiity service', function() {

    // @todo mock this
    let dateService         = new DateService();
    let availabilityService = new AvailabilityService(dateService);

    it('it_should_sum_the_total_amount_of_payments_made', function() {
       let payments = [
           { amount : 100 },
           { amount : 150 },
           { amount : 200 }
       ];
        let total = availabilityService.sumPayments(payments);
        expect(total).toBe(450);
    });

    it('it_should_extract_information_retrieved_by_accommodation_availability_and_format_into_2d_array_by_date_and_id', function() {
        let res = getFakeAccommodationAvailabilityData();
        expect(res, )
    });

});