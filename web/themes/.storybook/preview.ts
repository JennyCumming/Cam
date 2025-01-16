import "../css/tailwind.css";
import type { Preview } from "@storybook/html";

const fontStyles = `
  @font-face {
    font-family: 'Open Sans';
    src: url('/Open_Sans/OpenSans-Variable.ttf') format('truetype');
  }

  @font-face {
    font-family: 'feijoa';
    src: url('/Feijoa/feijoa-medium.woff2') format('woff2');
  }
`;

const style = document.createElement("style");
style.textContent = fontStyles;
document.head.appendChild(style);

export const Drupal = {
  behaviours: {},
};

const preview: Preview = {
  parameters: {
    server: {
      url: "http://storybook.ddev.site/storybook/stories/render",
    },
    controls: {
      matchers: {
        color: /(background|color)$/i,
        date: /Date$/i,
      },
    },
  },
};

export default preview;
