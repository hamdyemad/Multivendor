BNAIA DOCUMENTATION
===================

This directory contains comprehensive technical documentation for the Bnaia Multi-Vendor E-Commerce Platform.

VIEWING DOCUMENTATION
---------------------

Option 1: HTML Viewers (RECOMMENDED)
   Access the documentation through the HTML viewers for proper rendering:
   
   • Main Index: http://your-domain.com/docs/
   • Architecture: http://your-domain.com/docs/view-architecture.html
   • Database: http://your-domain.com/docs/view-database.html
   • API: http://your-domain.com/docs/view-api.html

Option 2: Raw Markdown Files
   You can also download the raw markdown files:
   
   • PROJECT_ARCHITECTURE_AND_STRATEGY.md
   • DATABASE_DESIGN.md
   • API_DOCUMENTATION.md
   
   Open these files in a markdown editor or IDE for proper UTF-8 rendering.

WHY USE HTML VIEWERS?
---------------------

The markdown files contain UTF-8 box-drawing characters (├──, └──, │) for tree structures.
When viewed directly in a browser as plain text, these characters may display incorrectly as "â"œâ"€â"€".

The HTML viewers use the Marked.js library to properly parse and render the markdown with correct UTF-8 encoding.

CONTENTS
--------

1. PROJECT_ARCHITECTURE_AND_STRATEGY.md
   - System architecture and design patterns
   - Module structure and organization
   - Core concepts (multi-language, attachments, bank products)
   - Data flow and lifecycle diagrams
   - Development guidelines

2. DATABASE_DESIGN.md
   - Complete database schema (80+ tables)
   - Entity relationship diagrams
   - Table structures and relationships
   - Indexes and constraints
   - Performance optimization strategies

3. API_DOCUMENTATION.md
   - RESTful API endpoints (60+)
   - Authentication and authorization
   - Request/response examples
   - Error handling
   - Code examples in multiple languages

4. openapi.json (in /public/api-docs/)
   - OpenAPI 3.0 specification
   - Machine-readable API documentation
   - Can be imported into Postman, Swagger, etc.

TECHNICAL DETAILS
-----------------

Framework: Laravel 12.x
Architecture: Modular Monolith
Database: MySQL 8.0+ with Redis caching
Languages: English, Arabic (Full RTL support)
Authentication: Laravel Sanctum

For questions or issues, contact the development team.
