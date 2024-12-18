# Project API Tools Documentation

This README documents the functionality of the various tools available in the project-api system.

## Table of Contents
- [Web Search Tools](#web-search-tools)
- [Terminal](#terminal)
- [Puppeteer Browser Automation](#puppeteer-browser-automation)
- [GitHub Integration](#github-integration)
- [File System Access](#file-system-access)
- [Knowledge Graph](#knowledge-graph)

## Web Search Tools
The `brave_web_search` tool allows searching the web using the Brave Search API. It's useful for finding general information, news articles, and web pages related to a query.

### Usage
```javascript
<function_calls>
<invoke name="brave_web_search">
<parameter name="query">SEARCH_QUERY</parameter>
</invoke>
</function_calls>
```