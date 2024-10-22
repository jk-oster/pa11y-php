import { viteBundler } from '@vuepress/bundler-vite'
import { defaultTheme } from '@vuepress/theme-default'
import { defineUserConfig } from 'vuepress'

export default defineUserConfig({
  bundler: viteBundler(),
  theme: defaultTheme({
    navbar: [
      { text: 'Installation', link: '/#installation-setup' },
      { text: 'Usage', link: '/usage.html' },
      { text: 'Author', link: 'https://jakobosterberger.com' },
      { text: 'Blog', link: 'https://jakobosterberger.com/posts' }
    ],

    repo: 'jk-oster/pa11y-php',
    docsBranch: 'gh-pages',
    docsDir: './docs',
    editLink: true,
    sidebarDepth: 2,
    sidebar: 'heading',
    home: '/',
    colorMode: 'auto',
  }),

  lang: 'en-US',
  title: 'Pa11y-PHP',
  description: 'A PHP wrapper for Pa11y. Easily generate accessibility reports for any page with PHP.',
  base: '/pa11y-php/',
});
