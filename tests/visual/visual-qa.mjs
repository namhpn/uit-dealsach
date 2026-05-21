import { chromium } from 'playwright';
import fs from 'node:fs/promises';
import path from 'node:path';

const baseUrl = (process.env.VISUAL_QA_BASE_URL || 'http://localhost:8080').replace(/\/$/, '');
const artifactDir = path.resolve(process.env.VISUAL_QA_ARTIFACT_DIR || 'build/visual-qa');

const viewports = [
  { name: 'desktop', width: 1440, height: 1000 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 },
];

const publicScenarios = [
  {
    name: 'home',
    path: '/',
    requiredText: ['DealSach', 'Sách có giá tốt', 'Nguồn dữ liệu so sánh'],
    landmarks: ['nav', 'main', 'footer'],
  },
  {
    name: 'catalog',
    path: '/sach?q=%C4%90%E1%BA%AFc&retailer%5B%5D=fahasa&category=van-hoc&stock=in_stock',
    requiredText: ['Danh mục sách', 'Bộ lọc', 'Kết quả'],
    landmarks: ['nav', 'main', 'aside'],
  },
  {
    name: 'detail',
    path: '/sach/dac-nhan-tam',
    requiredText: ['Đắc Nhân Tâm', 'So sánh giá', 'Theo dõi giảm giá'],
    landmarks: ['nav', 'main', 'table'],
  },
  {
    name: 'admin-login',
    path: '/ds-admin/login',
    requiredText: ['Quản trị DealSach', 'Đăng nhập', 'Tên đăng nhập'],
    landmarks: ['form'],
  },
];

const adminScenarios = [
  {
    name: 'admin-dashboard',
    path: '/ds-admin',
    requiredText: ['Bảng điều khiển', 'DealSach Admin'],
    landmarks: ['aside', 'header', 'main, .admin-content'],
  },
  {
    name: 'admin-books',
    path: '/ds-admin/books',
    requiredText: ['Quản lý sách', 'Thêm sách', 'Đắc Nhân Tâm'],
    landmarks: ['aside', 'header', 'table'],
  },
  {
    name: 'admin-book-form',
    path: '/ds-admin/books/new',
    requiredText: ['Thêm sách', 'Nhà xuất bản', 'Hiển thị trên trang công khai'],
    landmarks: ['aside', 'header', 'form'],
  },
];

const report = {
  baseUrl,
  generatedAt: new Date().toISOString(),
  results: [],
};

const failures = [];

await fs.rm(artifactDir, { recursive: true, force: true });
await fs.mkdir(artifactDir, { recursive: true });

const browser = await chromium.launch({ headless: true });

try {
  for (const viewport of viewports) {
    await runViewport(viewport);
  }
} finally {
  await browser.close();
}

await writeReport();

if (failures.length > 0) {
  console.error(`Visual QA failed with ${failures.length} issue(s).`);
  for (const failure of failures) {
    console.error(`- ${failure}`);
  }
  process.exit(1);
}

console.log(`Visual QA passed. Report: ${path.relative(process.cwd(), path.join(artifactDir, 'report.md'))}`);

async function runViewport(viewport) {
  const context = await browser.newContext({
    viewport: { width: viewport.width, height: viewport.height },
    deviceScaleFactor: 1,
    locale: 'vi-VN',
  });

  const page = await context.newPage();
  page.setDefaultTimeout(7000);
  page.setDefaultNavigationTimeout(12000);
  const consoleIssues = [];
  page.on('console', (message) => {
    if (['error', 'warning'].includes(message.type())) {
      consoleIssues.push(`${message.type()}: ${message.text()}`);
    }
  });
  page.on('pageerror', (error) => {
    consoleIssues.push(`pageerror: ${error.message}`);
  });

  for (const scenario of publicScenarios) {
    await auditScenario(page, viewport, scenario, consoleIssues);
  }

  await loginAdmin(page, viewport, consoleIssues);

  for (const scenario of adminScenarios) {
    await auditScenario(page, viewport, scenario, consoleIssues);
  }

  if (viewport.name === 'mobile') {
    await auditMobileMenus(page, viewport, consoleIssues);
  }

  await auditOtpInteraction(page, viewport, consoleIssues);

  await context.close();
}

async function auditScenario(page, viewport, scenario, consoleIssues) {
  const beforeConsoleCount = consoleIssues.length;
  const url = `${baseUrl}${scenario.path}`;
  await gotoReady(page, url);

  const issues = [];
  const bodyText = await page.locator('body').innerText();

  for (const text of scenario.requiredText) {
    if (!bodyText.includes(text)) {
      issues.push(`missing visible text "${text}"`);
    }
  }

  for (const selector of scenario.landmarks) {
    if (await page.locator(selector).count() === 0) {
      issues.push(`missing landmark ${selector}`);
    }
  }

  issues.push(...await page.evaluate(runVisualAssertions));

  const newConsoleIssues = consoleIssues.slice(beforeConsoleCount);
  if (newConsoleIssues.length > 0) {
    issues.push(...newConsoleIssues);
  }

  const screenshot = await saveScreenshot(page, `${viewport.name}-${scenario.name}.png`, true);
  pushResult(viewport, scenario.name, url, screenshot, issues);
}

async function loginAdmin(page, viewport, consoleIssues) {
  const beforeConsoleCount = consoleIssues.length;
  await gotoReady(page, `${baseUrl}/ds-admin/login`);
  await page.locator('#loginUsername').fill('admin');
  await page.locator('#loginPassword').fill('123456');
  await Promise.all([
    page.waitForURL(/\/ds-admin(?:\/)?$/, { waitUntil: 'domcontentloaded' }),
    page.locator('#loginSubmitBtn').click(),
  ]);
  await page.locator('.admin-main').waitFor({ state: 'visible' });

  const issues = [];
  if (!page.url().replace(/\/$/, '').endsWith('/ds-admin')) {
    issues.push('admin login did not land on dashboard');
  }

  issues.push(...consoleIssues.slice(beforeConsoleCount));
  const screenshot = await saveScreenshot(page, `${viewport.name}-admin-login-flow.png`, true);
  pushResult(viewport, 'admin-login-flow', page.url(), screenshot, issues);
}

async function auditMobileMenus(page, viewport, consoleIssues) {
  const publicIssues = [];
  const beforePublic = consoleIssues.length;
  await gotoReady(page, `${baseUrl}/`);
  await page.locator('.navbar-toggler').click();
  await page.locator('#publicNav.show').waitFor({ state: 'visible', timeout: 3000 });
  await page.waitForTimeout(350);
  publicIssues.push(...await page.evaluate(runVisualAssertions));
  publicIssues.push(...consoleIssues.slice(beforePublic));
  const publicShot = await saveScreenshot(page, `${viewport.name}-public-menu-open.png`, false);
  pushResult(viewport, 'public-menu-open', page.url(), publicShot, publicIssues);

  const adminIssues = [];
  const beforeAdmin = consoleIssues.length;
  await gotoReady(page, `${baseUrl}/ds-admin/books`);
  await page.locator('#sidebarToggleBtn').click();
  await page.locator('#adminSidebar.show').waitFor({ state: 'visible', timeout: 3000 });
  await page.waitForTimeout(350);
  adminIssues.push(...await page.evaluate(runVisualAssertions));
  adminIssues.push(...consoleIssues.slice(beforeAdmin));
  const adminShot = await saveScreenshot(page, `${viewport.name}-admin-menu-open.png`, false);
  pushResult(viewport, 'admin-menu-open', page.url(), adminShot, adminIssues);
}

async function auditOtpInteraction(page, viewport, consoleIssues) {
  const beforeConsoleCount = consoleIssues.length;
  await gotoReady(page, `${baseUrl}/sach/dac-nhan-tam`);

  const email = `visual-qa-${viewport.name}-${Date.now()}@example.com`;
  const issues = [];
  await page.locator('#trackingEmail').fill(email);
  await page.locator('#targetPrice').fill('99000');
  await page.locator('#otpRequestForm button[type="submit"]').click();
  await page.locator('#otpVerifyForm:not([hidden])').waitFor({ state: 'visible', timeout: 5000 });

  const hint = await page.locator('#devOtpHint').innerText();
  const match = hint.match(/\d{6}/);
  if (!match) {
    issues.push('OTP request did not expose a six-digit dev OTP');
  } else {
    await page.locator('#otpCode').fill(match[0]);
    await page.locator('#otpVerifyForm button[type="submit"]').click();
    await page.locator('#trackingCreateForm:not([hidden])').waitFor({ state: 'visible', timeout: 5000 });
  }

  issues.push(...await page.evaluate(runVisualAssertions));
  issues.push(...consoleIssues.slice(beforeConsoleCount));
  const screenshot = await saveScreenshot(page, `${viewport.name}-otp-flow-ready.png`, true);
  pushResult(viewport, 'otp-flow-ready', page.url(), screenshot, issues);
}

async function gotoReady(page, url) {
  await page.goto(url, { waitUntil: 'domcontentloaded' });
  await page.locator('body').waitFor({ state: 'visible' });
}

async function saveScreenshot(page, fileName, fullPage) {
  const outputPath = path.join(artifactDir, fileName);
  const buffer = await page.screenshot({ path: outputPath, fullPage });
  if (buffer.length < 5000) {
    failures.push(`${fileName}: screenshot looks too small (${buffer.length} bytes)`);
  }
  return path.relative(process.cwd(), outputPath).replaceAll('\\', '/');
}

function pushResult(viewport, scenario, url, screenshot, issues) {
  const result = {
    viewport: viewport.name,
    size: `${viewport.width}x${viewport.height}`,
    scenario,
    url,
    screenshot,
    issues,
  };
  report.results.push(result);

  for (const issue of issues) {
    failures.push(`${viewport.name}/${scenario}: ${issue}`);
  }
}

async function writeReport() {
  await fs.writeFile(
    path.join(artifactDir, 'report.json'),
    `${JSON.stringify(report, null, 2)}\n`,
    'utf8',
  );

  const lines = [
    '# Visual QA Report',
    '',
    `Base URL: ${report.baseUrl}`,
    `Generated: ${report.generatedAt}`,
    '',
    '| Viewport | Scenario | Status | Screenshot |',
    '| --- | --- | --- | --- |',
  ];

  for (const result of report.results) {
    const status = result.issues.length === 0 ? 'PASS' : `FAIL (${result.issues.length})`;
    lines.push(`| ${result.viewport} ${result.size} | ${result.scenario} | ${status} | [png](${result.screenshot}) |`);
    for (const issue of result.issues) {
      lines.push(`|  |  | ${escapeTable(issue)} |  |`);
    }
  }

  lines.push('');
  await fs.writeFile(path.join(artifactDir, 'report.md'), `${lines.join('\n')}\n`, 'utf8');
}

function escapeTable(value) {
  return value.replaceAll('|', '\\|').replace(/\s+/g, ' ').trim();
}

function runVisualAssertions() {
  const issues = [];
  const root = document.documentElement;
  const body = document.body;
  const viewportWidth = window.innerWidth;
  const viewportHeight = window.innerHeight;
  const bodyText = body.innerText || '';

  const badTextMarkers = [
    'CodeIgniter\\Exceptions',
    'ErrorException',
    'Whoops',
    'Ã',
    'áº',
    'Ä',
    'â€¢',
    'â”',
    'â•',
  ];

  for (const marker of badTextMarkers) {
    if (bodyText.includes(marker)) {
      issues.push(`rendered text contains "${marker}"`);
    }
  }

  if (root.scrollWidth > viewportWidth + 2) {
    issues.push(`horizontal page overflow: scrollWidth ${root.scrollWidth}, viewport ${viewportWidth}`);
  }

  if (body.getBoundingClientRect().height < Math.min(320, viewportHeight * 0.5)) {
    issues.push('body content height is unexpectedly small');
  }

  const brokenImages = Array.from(document.images)
    .filter((img) => img.offsetParent !== null && (!img.complete || img.naturalWidth === 0 || img.naturalHeight === 0))
    .map((img) => img.getAttribute('src') || img.getAttribute('alt') || 'image');
  if (brokenImages.length > 0) {
    issues.push(`broken visible image(s): ${brokenImages.slice(0, 3).join(', ')}`);
  }

  const fixedHeader = document.querySelector('.ds-navbar.sticky-top, .admin-topbar');
  const main = document.querySelector('main, .admin-content');
  if (fixedHeader && main && window.scrollY === 0) {
    const headerRect = fixedHeader.getBoundingClientRect();
    const mainRect = main.getBoundingClientRect();
    if (mainRect.top < headerRect.bottom - 4 && mainRect.bottom > headerRect.bottom) {
      issues.push('top navigation appears to overlap main content');
    }
  }

  const sidebar = document.querySelector('.admin-sidebar');
  const adminMain = document.querySelector('.admin-main');
  if (sidebar && adminMain && viewportWidth >= 992) {
    const sidebarRect = sidebar.getBoundingClientRect();
    const mainRect = adminMain.getBoundingClientRect();
    if (mainRect.left < sidebarRect.right - 2) {
      issues.push('admin sidebar overlaps desktop content');
    }
  }

  const candidates = Array.from(document.querySelectorAll([
    'button',
    '.btn',
    '.badge',
    '.nav-link',
    '.navbar-brand',
    'label',
    'th',
    'td',
    '.form-control',
    '.form-select',
  ].join(',')));

  for (const element of candidates) {
    if (!isVisible(element)) {
      continue;
    }

    if (viewportWidth < 992 && element.closest('.admin-sidebar:not(.show)')) {
      continue;
    }

    const rect = element.getBoundingClientRect();
    if (rect.width <= 0 || rect.height <= 0) {
      continue;
    }

    if (!element.closest('.table-responsive') && (rect.left < -2 || rect.right > viewportWidth + 2)) {
      issues.push(`element exits viewport: ${describeElement(element)}`);
      break;
    }

    const style = window.getComputedStyle(element);
    const allowsHorizontalScroll = ['auto', 'scroll'].includes(style.overflowX);
    if (!allowsHorizontalScroll && element.scrollWidth > element.clientWidth + 2 && style.whiteSpace !== 'normal') {
      issues.push(`text/control may be clipped: ${describeElement(element)}`);
      break;
    }

    const isInteractive = element.matches('button, a.btn, .nav-link, input, select');
    if (isInteractive && (rect.width < 28 || rect.height < 28)) {
      issues.push(`interactive target is too small: ${describeElement(element)} (${Math.round(rect.width)}x${Math.round(rect.height)})`);
      break;
    }
  }

  const cards = Array.from(document.querySelectorAll('.ds-card, .card, .login-card'));
  for (const card of cards) {
    if (!isVisible(card)) {
      continue;
    }
    const rect = card.getBoundingClientRect();
    if (rect.width > viewportWidth + 2) {
      issues.push(`card wider than viewport: ${describeElement(card)}`);
      break;
    }
  }

  return issues;

  function isVisible(element) {
    const style = window.getComputedStyle(element);
    const rect = element.getBoundingClientRect();
    return style.visibility !== 'hidden'
      && style.display !== 'none'
      && Number(style.opacity) !== 0
      && rect.width > 0
      && rect.height > 0;
  }

  function describeElement(element) {
    const text = (element.innerText || element.value || element.getAttribute('aria-label') || element.getAttribute('title') || element.tagName)
      .replace(/\s+/g, ' ')
      .trim()
      .slice(0, 60);
    const id = element.id ? `#${element.id}` : '';
    const className = typeof element.className === 'string'
      ? `.${element.className.trim().replace(/\s+/g, '.')}`.replace(/\.$/, '')
      : '';
    return `${element.tagName.toLowerCase()}${id}${className}${text ? ` "${text}"` : ''}`;
  }
}
