/**
 * Define your FAQ question and answers here.
 * Don't forget all the right commas in the right places!
 */
var faq = [
	{
		question: "How do I edit the FAQ?",
		answer: "<p><strong>Have a look in the source code of the help tab.</strong></p><p>You can edit and add questions and anwers in the <code>script.js</code> file (in the js folder).</p><p>You can even use <strong>HTML tags</strong> to <em>format</em> your <u>answers</u>!</p>"
	},
	{
		question: "Dummy Question",
		answer: "That's your dummy answer right there."
	},
];

$(function() {
	var faqTemplate = Handlebars.compile($('#faq-template').html());
	$('#accordion').html(faqTemplate({faq: faq}));
});
