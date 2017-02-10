import { PublicHtmlPage } from './app.po';

describe('public-html App', function() {
  let page: PublicHtmlPage;

  beforeEach(() => {
    page = new PublicHtmlPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
