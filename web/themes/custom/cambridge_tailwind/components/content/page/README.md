# Basic Page Component

## Overview

This document explains how the basic page component is rendered within the Cambridge University theme, and it should be updated when required.

## Template Hierarchy

```
templates/layout/page.html.twig
└── templates/content/node--page.html.twig
    └── components/content/page/page.twig
```

### Template Details

1. `page.html.twig`

   - Provides the outer page structure with the different page regions
   - Includes global header, content (where the basic page content will reside) and the footer
   - Passes page regions to the node template

2. `node--page.html.twig`

   - Processes node-specific fields for the basic page
   - Prepares variables for the page component
   - Removes fields that are handled separately to avoid duplicate rendering

3. `page.twig`
   - The basic page component which handles the actual layout and rendering of the basic page content
   - Implements responsive design using Tailwind classes
   - Manages the right-hand sidebar visibility at different breakpoints

## Content Handling

### Page Title

In order to keep the basic page elements within the same flexbox container, the page title is handled differently for basic pages compared with other content types:

- The standard `page-title.html.twig` template is set to null for basic pages
- Title rendering is handled directly in `page.twig` component file
- This is configured in `cambridge_tailwind_preprocess_page_title()` in cambridge_tailwind.theme

## Fields and Variables

### Required Variables

- `title`: The page title (h1)
- `content`: Main content area excluding specific fields

### Optional Variables

- `page_summary`: Summary text for the page
- `top_content`: Content displayed at the top of the page
- `paragraphs`: Main paragraph content
- `bottom_paragraphs`: Bottom paragraph content
- `sidebar_items`: Right-hand sidebar content (visible from xl breakpoint)

## Responsive Layout

The component uses Tailwind CSS classes for its responsive behaviour:

### Container

```html
<div class="sm:px-6 mx-auto max-w-screen-2xl px-4 lg:px-8"></div>
```

- Maximum width of 1536px
- Responsive padding at different breakpoints

### Layout Structure

```html
<div class="flex flex-col xl:flex-row xl:space-x-8"></div>
```

- Stacked layout on mobile (flex-col)
- Side-by-side at xl breakpoint (xl:flex-row)
- 8 units of space between columns at xl

### Right Sidebar

```html
<div class="xl:w-328 hidden xl:block xl:flex-none xl:pl-64"></div>
```

- Hidden by default (hidden)
- Visible at xl breakpoint (xl:block)
- Fixed width of 328px (xl:w-328)
- Left padding of 64px (xl:pl-64)

## Breakpoints

The component follows these breakpoints:

- xs: < 520px
- sm: 520px - 767px
- md: 768px - 1023px
- lg: 1024px - 1279px
- xl: > 1280px

## Visual Structure

```
┌─────────────────────────────────────────────────────────────┐
│ page.html.twig                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ node--page.html.twig                                    │ │
│ │ ┌────────────────────────────────────────────────────┐  │ │
│ │ │ page.twig                             ┌─────────┐  │  │ │
│ │ │ ┌──────────────────────────────────   │         │  │  │ │
│ │ │ │ Title (h1)                       │  │         │  │  │ │
│ │ │ ├──────────────────────────────────┤  │         │  │  │ │
│ │ │ │ Top Content                      │  │ Right   │  │  │ │
│ │ │ ├──────────────────────────────────┤  │ Sidebar │  │  │ │
│ │ │ │ Main Paragraphs                  │  │ (xl+)   │  │  │ │
│ │ │ ├──────────────────────────────────┤  │         │  │  │ │
│ │ │ │ Content                          │  │         │  │  │ │
│ │ │ ├──────────────────────────────────┤  │         │  │  │ │
│ │ │ │ Bottom Paragraphs                │  │         │  │  │ │
│ │ │ └──────────────────────────────────┘  └─────────┘  │  │ │
│ │ └────────────────────────────────────────────────────┘  │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

## Usage Example

```twig
{% include '@cambridge_tailwind/components/content/page/page.twig' with {
  title: 'Page Title',
  content: content,
  page_summary: page_summary,
  top_content: top_content,
  paragraphs: paragraphs,
  bottom_paragraphs: bottom_paragraphs,
  sidebar_items: sidebar_items
} %}
```

## Implementation Notes

This component should be implemented according to the Figma design specifications:

- Responsive layout behaviour across breakpoints (xs: 520px to xl: 1536px)
- Right-hand sidebar placement and visibility (appears at xl breakpoint 1280px+), collapsed below this
- The left-hand navigation block is to be handled by a separate template file - until this is implemented the basic page component content will appear collapsed to the left, even on larger screens
- Typography and vertical spacing of page component elements is yet to be implemented
