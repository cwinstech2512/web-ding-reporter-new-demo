# web_ding_reporter_new

Laravel + Vue 2 + Vuetify 的工程檢測報告系統。本 repository 保留完整前後端原始碼；GitHub Pages 只發布不需要 Laravel、資料庫或 API 的純前端展示版本。

## 前端展示模式

```bash
npm install
npm run build:demo
```

完成後的靜態網站位於 `pages-dist/`。展示模式會：

- 自動使用本機展示帳號，不需要登入後端。
- 依照 Laravel route、Controller、Resource、Service 與 Model 契約，由本機 Axios adapter 提供儀表板、帳號、角色、派單、參照表與報告展示資料。
- 使用 hash router，支援 GitHub Pages repository 子路徑。
- 顯示 `FRONTEND DEMO · LOCAL DATA` 標記。
- 不會向 Laravel API 發出請求。

環境變數範例在 [`.env.frontend.example`](.env.frontend.example)。安全預設為
`MIX_FRONTEND_DEMO=false`；`npm run build:demo` 只在該次建置明確開啟展示模式，
因此一般 `npm run production` 仍維持原本的真實 API 流程。

## 原有開發模式

Laravel 與資料庫環境準備完成後：

```bash
composer install
npm install
php artisan serve
npm run watch
```

原有 API、Laravel controller、migration、service 與報表功能皆保留，前端展示模式不會刪除或取代它們。

Mock 與後端契約的對照範圍請見 [`docs/FRONTEND_DEMO.md`](docs/FRONTEND_DEMO.md)。

## 驗證與建置

```bash
npm run verify:paths
npm run lint:check
npm run build:demo
npm run production
```

`verify:paths` 會在 macOS 上預先檢查 Linux 大小寫敏感的 import、Vue 樣板素材與 CSS URL，避免 GitHub Actions 才發生缺檔。

## GitHub Pages

推送 `main` 後，`.github/workflows/deploy-pages.yml` 會自動檢查並部署 `pages-dist/`。Repository 的 `Settings → Pages → Build and deployment` Source 必須設為 `GitHub Actions`。

## 資料安全

`.env`、`vendor`、`node_modules`、建置產物與執行期上傳資料不應加入新的 Git commit。既有 Git 追蹤的範例檔案仍保留於專案歷史中。
