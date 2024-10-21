import { viteBundler } from '@vuepress/bundler-vite'
import { defaultTheme } from '@vuepress/theme-default'
import { defineUserConfig } from 'vuepress'

export default defineUserConfig({
  bundler: viteBundler(),
  theme: defaultTheme(),

  lang: 'en-US',
  title: 'Pa11y-PHP',
  description: 'A PHP wrapper for Pa11y. Easily generate accessibility reports for any page with PHP.',
  base: '/pa11y-php/',
});
