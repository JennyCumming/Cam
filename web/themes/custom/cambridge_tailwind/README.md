# Cambridge Tailwind Theme

## Description

This repository contains the Cambridge theme for [Drupal](https://www.drupal.org/) 10 implementations.

This theme was initially generated from Drupal's starterkit_theme. Additional information on generating themes can be found in the [Starterkit documentation](https://www.drupal.org/docs/core-modules-and-themes/core-themes/starterkit-theme).

Styles are based on the [tailwind](https://tailwindcss.com/) CSS framework.

It also includes twig templates to be implemented as [drupal single directory components](https://www.drupal.org/docs/develop/theming-drupal/using-single-directory-components) with the above theme. These are documented using [Storybook](https://storybook.js.org/).

## Developer guide

### Requirements

Local development requires this repo to be used as part of the Cambridge D10+Developer starter repo. The starter repo provides a full local Drupal installation. This Drupal install is required to process Twig based Storybook stories and to render Twig components in Storybook via the Drupal renderer:
https://gitlab.developers.cam.ac.uk/uis/devops/webcms/2023-platform/cambridge-initial-composer-file

You must have the following installed to run the pre-commit hooks:

- `node` globally
- `npm` globally
- [`pre-commit`](https://pre-commit.com/) globally

### Setup

To install all local dependencies, run:

```bash
ddev npm install
```

To install all pre-commit hooks, run:

```bash
pre-commit install
```

This will perform checks defined in `.pre-commit-config.yaml` before committing, including:

- Running prettier & eslint for code formatting and linting
- Consistent file endings / no trailing whitespace
- Consistent formatting of yaml and json files
- etc.

To run these checks manually, run `pre-commit run -a`.

It is recommended you install the [eslint](https://marketplace.visualstudio.com/items?itemName=dbaeumer.vscode-eslint), [prettier](https://marketplace.visualstudio.com/items?itemName=esbenp.prettier-vscode) and [typescript](https://marketplace.visualstudio.com/items?itemName=ms-vscode.vscode-typescript-next) VSCode extensions. These will read in our configuration, warn in your editor when you are breaking rules, and auto-format on save.

### Developing changes locally

#### Components

Build stories

```bash
ddev drush storybook:generate-all-stories
```

This generates:
`components/**/*.stories.json`

Start Storybook server and watch Twig changes with:

```bash
ddev npm run watch
```

This will run storybook on http://cam.ddev.site:6006/

Changes made to components will live update. Components must have a `components/**/*.stories.twig` file defining them in order to appear in storybook.

#### Theme

To develop changes against the tailwind theme, run:

```bash
ddev npm run dev
```

This is configured to watch for changes.

### Formatting and linting

To independently test for any linting errors `eslint` has found, run:

```bash
ddev npm run test:lint
```

To independently test for any code formatting errors `prettier` has found, run:

```bash
ddev npm run test:format
```

And to manually format the whole repo, run:

```bash
ddev npm run format
```

### Debugging

You can add `{{ dump(_context) }}` into a twig component to print out a block containing all the props being passed through and variables set.

Working in this repo when composer has pulled it over HTTPS can cause Git to want to connect to Gitlab via HTTPS. To reset this to consistently go over SSH, and be able to push up to the repo again, run:

```bash
git remote set-url origin git@gitlab.developers.cam.ac.uk:uis/devops/webcms/2023-platform/cambridge_tailwind.git
```

### Testing

> There is currently no test coverage on this repository.

### Deployment

> The Drupal 10 platform that utilises this theme is still in development and has not yet been deployed to production.

#### Releases

> A release candidate has not yet been published.

#### Using in Drupal instances

The version of this theme pulled in to Drupal 10 instances is defined in [composer.json](https://gitlab.developers.cam.ac.uk/uis/devops/webcms/2023-platform/cambridge-initial-composer-file/-/blob/main/composer.json) in the intial composer file repo.

## Related links

- [Drupal 10 Cambridge Profile](https://gitlab.developers.cam.ac.uk/uis/devops/webcms/2023-platform/drupal-10-cambridge-profile) - the Drupal profile that contains our drupal configuration and implementations of components.
- [Cambridge initial composer file](https://gitlab.developers.cam.ac.uk/uis/devops/webcms/2023-platform/cambridge-initial-composer-file/) - the composer configuration that pulls together this theme with the drupal profile.
