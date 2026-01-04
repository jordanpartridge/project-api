const puppeteer = require('puppeteer');

async function testServices() {
  const browser = await puppeteer.launch({ 
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  try {
    console.log('Testing services...');
    const services = [
      { url: 'http://api.test', name: 'Laravel API' },
      { url: 'http://claude.test', name: 'Claude WebUI' },
      { url: 'http://ollama.test', name: 'Ollama WebUI' }
    ];

    for (const service of services) {
      const page = await browser.newPage();
      console.log(`Testing ${service.name} at ${service.url}...`);
      
      try {
        const response = await page.goto(service.url);
        console.log(`${service.name}: ${response.status()} ${response.statusText()}`);
        
        // Take screenshot
        await page.screenshot({ 
          path: `${service.name.toLowerCase().replace(' ', '-')}-test.png`,
          fullPage: true 
        });
      } catch (error) {
        console.error(`Error testing ${service.name}:`, error.message);
      }
      
      await page.close();
    }
    
  } catch (error) {
    console.error('Error during tests:', error);
  } finally {
    await browser.close();
  }
}

testServices();