import { viteBundler } from '@vuepress/bundler-vite'
import { defaultTheme } from '@vuepress/theme-default'
import { defineUserConfig } from 'vuepress'

export default defineUserConfig({
  bundler: viteBundler(),
  theme: defaultTheme({
    navbar: [
      { text: 'Code', link: 'https://github.com/jk-oster/pa11y-php' },
      { text: 'Author', link: 'https://jakobosterberger.com' },
      { text: 'Blog', link: 'https://jakobosterberger.com/posts' }
    ]
  }),

  lang: 'en-US',
  title: 'Pa11y-PHP',
  description: 'A PHP wrapper for Pa11y. Easily generate accessibility reports for any page with PHP.',
  base: '/pa11y-php/',
});
