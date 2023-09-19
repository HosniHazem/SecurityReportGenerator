const puppeteer = require('puppeteer-core');

const [, , htmlPath, imagePath] = process.argv;

(async () => {
    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.goto(`file://${htmlPath}`);
    await page.screenshot({ path: imagePath });
    await browser.close();
})();
