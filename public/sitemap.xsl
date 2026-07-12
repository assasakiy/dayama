<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="3.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    exclude-result-prefixes="xhtml">

<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:variable name="site-name" select="'Modern Blog'"/>
<xsl:variable name="path" select="/sitemap:urlset/sitemap:url[1]/sitemap:loc"/>

<xsl:template match="/">
<html lang="en">
<head>
    <title>
        <xsl:choose>
            <xsl:when test="local-name(/*) = 'sitemapindex'">Sitemap Overview</xsl:when>
            <xsl:otherwise>Sitemap — <xsl:value-of select="$site-name"/></xsl:otherwise>
        </xsl:choose>
    </title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            padding: 2rem 1rem;
            line-height: 1.5;
        }
        .container { max-width: 740px; margin: 0 auto; }
        header { margin-bottom: 2rem; }
        h1 { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; }
        .subtitle { color: #666; font-size: 0.875rem; margin-top: 0.25rem; }
        .nav-links { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem; }
        .nav-links a {
            padding: 0.375rem 0.75rem;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            font-size: 0.8125rem;
            color: #2563eb;
            text-decoration: none;
            background: #fff;
        }
        .nav-links a:hover { background: #f0f0f0; }
        .nav-links a.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .card {
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .card-header {
            padding: 0.75rem 1rem;
            background: #fafafa;
            border-bottom: 1px solid #e5e5e5;
            font-size: 0.8125rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header .count { font-weight: 400; color: #999; }
        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.625rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            gap: 1rem;
        }
        .row:last-child { border-bottom: none; }
        .row:hover { background: #fafafa; }
        .row a {
            font-size: 0.8125rem;
            color: #2563eb;
            text-decoration: none;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .row a:hover { text-decoration: underline; }
        .row .meta {
            font-size: 0.75rem;
            color: #999;
            white-space: nowrap;
            flex-shrink: 0;
        }
        footer {
            text-align: center;
            font-size: 0.75rem;
            color: #999;
            margin-top: 2rem;
        }
        footer a { color: #666; text-decoration: none; }
        footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <xsl:variable name="section">
        <xsl:choose>
            <xsl:when test="contains($path, 'sitemap-pages')">pages</xsl:when>
            <xsl:when test="contains($path, 'sitemap-posts')">posts</xsl:when>
            <xsl:when test="contains($path, 'sitemap-categories')">categories</xsl:when>
            <xsl:when test="contains($path, 'sitemap-tags')">tags</xsl:when>
            <xsl:otherwise>overview</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <header>
        <h1>
            <xsl:choose>
                <xsl:when test="$section = 'overview'">Sitemap Overview</xsl:when>
                <xsl:otherwise><xsl:value-of select="concat(translate(substring($section, 1, 1), 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'), substring($section, 2))"/> Sitemap</xsl:otherwise>
            </xsl:choose>
        </h1>
        <p class="subtitle"><xsl:value-of select="$site-name"/></p>
    </header>

    <div class="nav-links">
        <a href="/sitemap.xml"><xsl:if test="$section = 'overview'"><xsl:attribute name="class">active</xsl:attribute></xsl:if>Overview</a>
        <a href="/sitemap-pages.xml"><xsl:if test="$section = 'pages'"><xsl:attribute name="class">active</xsl:attribute></xsl:if>Pages</a>
        <a href="/sitemap-posts.xml"><xsl:if test="$section = 'posts'"><xsl:attribute name="class">active</xsl:attribute></xsl:if>Posts</a>
        <a href="/sitemap-categories.xml"><xsl:if test="$section = 'categories'"><xsl:attribute name="class">active</xsl:attribute></xsl:if>Categories</a>
        <a href="/sitemap-tags.xml"><xsl:if test="$section = 'tags'"><xsl:attribute name="class">active</xsl:attribute></xsl:if>Tags</a>
    </div>

    <div class="card">
        <xsl:choose>
            <xsl:when test="local-name(/*) = 'sitemapindex'">
                <div class="card-header">Sitemaps <span class="count"><xsl:value-of select="count(//sitemap:sitemap)"/></span></div>
                <xsl:for-each select="//sitemap:sitemap">
                    <div class="row">
                        <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </div>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise>
                <div class="card-header">
                    <xsl:choose>
                        <xsl:when test="$section = 'pages'">Pages</xsl:when>
                        <xsl:when test="$section = 'posts'">Posts</xsl:when>
                        <xsl:when test="$section = 'categories'">Categories</xsl:when>
                        <xsl:when test="$section = 'tags'">Tags</xsl:when>
                        <xsl:otherwise>URLs</xsl:otherwise>
                    </xsl:choose>
                    <span class="count"><xsl:value-of select="count(//sitemap:url)"/></span>
                </div>
                <xsl:for-each select="//sitemap:url">
                    <div class="row">
                        <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                        <xsl:if test="sitemap:lastmod">
                            <span class="meta"><xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/></span>
                        </xsl:if>
                    </div>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </div>

    <footer>
        <p>Generated by <a href="/"><xsl:value-of select="$site-name"/></a></p>
    </footer>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>