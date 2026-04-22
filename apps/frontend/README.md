# Frontend App

Bu klasör Faz 1 itibarıyla gerçekten çalışan bir Next.js public frontend iskeleti içerir.

Şu an çalışan parçalar:

- slug bazlı route çözümü
- Laravel public API client
- site/theme/settings/menu çekme
- backend-driven Basic HTML Section Mode render
- theme token'larını CSS variable olarak uygulama

Ana bağımlılık:

- `CMS_API_URL`

Örnek local env:

```env
CMS_API_URL=http://127.0.0.1:8000
```

Çalıştırma:

```bash
cd /Volumes/Dev/iraspa-cms/apps/frontend
cp .env.example .env.local
npm install
npm run dev
```

Detaylı tam akış için:

- [docs/local-fullstack-runbook.md](../docs/local-fullstack-runbook.md)
