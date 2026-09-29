# 前端展示 Mock 契約

展示資料不是任意產生，而是依據專案既有 Laravel 後端程式碼建立：

- API 路徑：`routes/api.php`
- 包裝格式：`App\Http\Resources\ResponseResource`
- 欄位來源：`App\Models`、`App\Repositories` 與 `App\Services`
- 帳號／角色：`UserController`、`RoleService`、`MenuResource`、`RoleResource`
- 派單／報告：`DispatchController`、`DispatchService`、`DispatchRepository` 與各種 `Report*Resource`
- 參照表：`ImportController`、`ImportService` 及 RT／RTPR／射源 Repository

## 展示覆蓋

| 前端功能 | 後端契約 | 展示資料 |
| --- | --- | --- |
| 身分與選單 | `api/user`、`api/login`、User `menu` accessor | 管理員展示身分及既有選單結構 |
| 儀表板 | `api/analytics`、`api/analytics/dispatch` | 四種後端狀態、12 個月與近 7 日統計 |
| 帳號／權限 | `api/account`、`api/roles`、`api/roles_all`、`api/menu` | 使用者、角色與 MenuResource 欄位 |
| 派單 | `api/dispatch/data/list`、`api/dispatch/data/first/{id}` | 對應 Dispatch model 欄位的展示案件 |
| 報告 | `api/dispatch/{corporation}/{command}` | PT／RT／MT／UT／IT／VT 的 dispatch 與 `report_*` 關聯 |
| 參照匯入 | `api/import/data/{type}`、`api/data/{type}/lately` | 射源、RT 鋼板厚度、RTPR 管厚度與 OD |
| 範例與驗證 | `api/sample/*`、`api/dispatch_verify*` | 空範例紀錄與本機成功狀態 |
| 台電報告 | `api/taipower/data/*` | IMHQST 與每日報告列表欄位 |

展示模式只保證前端畫面、導覽與主要資料結構可呈現；檔案解析、Word／PDF 匯出、真實資料庫交易與審核流程仍屬 Laravel 後端功能，不會在 GitHub Pages 執行。

所有展示 API 都由 `resources/js/demo/adapter.js` 在瀏覽器內直接回應，不會送出後端網路請求。
