import { defineConfig } from 'astro/config';
import tailwind from '@astrojs/tailwind';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const deployedSite = path.resolve(__dirname, '../deployed_site');

const mimeTypes = {
  '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png',
  '.gif': 'image/gif', '.webp': 'image/webp', '.svg': 'image/svg+xml',
};

function serveDeployedSite() {
  return {
    name: 'serve-deployed-site',
    apply: 'serve',
    configureServer(server) {
      server.middlewares.use((req, res, next) => {
        const filePath = path.join(deployedSite, req.url.split('?')[0]);
        if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
          const ext = path.extname(filePath).toLowerCase();
          res.setHeader('Content-Type', mimeTypes[ext] || 'application/octet-stream');
          fs.createReadStream(filePath).pipe(res);
        } else {
          next();
        }
      });
    },
  };
}

export default defineConfig({
  outDir: '../deployed_site',
  build: {
    assets: '_astro',
  },
  vite: {
    envDir: '../',
    plugins: [serveDeployedSite()],
    build: {
      emptyOutDir: false,
    },
    server: {
      proxy: {
        '/contact.php': 'http://localhost:8084',
      },
    },
  },
  integrations: [tailwind()],
});
