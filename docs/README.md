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

## File System Access
The file system tools allow reading, writing, and manipulating files and directories within the project.

Key capabilities:
- List directory contents with `list_directory`
- Read files with `read_file` 
- Write files with `write_file`
- Check file info with `get_file_info`
- Search for files by name with `search_files`

Example usage:
```javascript
<function_calls>
<invoke name="read_file">
<parameter name="path">/path/to/file.md</parameter>
</invoke>
</function_calls>
```