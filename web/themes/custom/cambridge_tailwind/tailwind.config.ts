import type { Config } from "tailwindcss";
import typography from "@tailwindcss/typography";
import forms from "@tailwindcss/forms";
import { fontFamily } from "tailwindcss/defaultTheme";
import type { CSSRuleObject, PluginAPI } from "tailwindcss/types/config";

const colors = {
  white: "#FFFFFF",
  black: "#010A14",
  "blue-black": "#081B21",
  "pure-black": "#000000",
  blue: {
    50: "#F6FEFE",
    75: "#DEF3F1",
    100: "#C5EEEA",
    200: "#92E2E1",
    300: "#6FCFCD",
    400: "#30B7B7",
    500: "#119BA0",
    600: "#0C7381",
    700: "#034553",
    800: "#06333C",
    900: "#0F2125",
  },
  green: {
    100: "#F7F8F7",
    200: "#EBEFEE",
  },
  cherry: {
    100: "#AA0943",
    200: "#910839",
    300: "#74062E",
  },
};

export default {
  content: [
    "./templates/**/*.{html,twig,ts}",
    "./components/**/*.{html,twig,ts}",
    "./cambridgeComponents/**/*.{html,twig}",
  ],
  safelist: [
    ...[
      // Annoyingly since we auto-generate the prose classes, tailwind doesn't detect them
      // And so won't generate the CSS for them
      // So we need to safelist the required subset of them here
      // Done it in such a way to easily add more if needed without having to understand the quirks of prose...
      "underline-offset-4",
      "decoration-1",
      "underline",
      "hover:underline",
      "active:underline",
      "active:bg-transparent",
      "active:shadow-none",
      "text-link-default",
      "hover:text-link-hover",
      "hover:decoration-link-default",
      "active:text-link-active",
      "active:decoration-link-default",
    ].map((className) => {
      const splitClassName = className.split(":");
      if (splitClassName.length === 1) {
        return `prose-a:${className}`;
      } else {
        return `${splitClassName[0]}:prose-a:${splitClassName[1]}`;
      }
    }),
    "stroke-current",
    "md:max-w-[800px]",
    "empty:hidden",
  ],
  theme: {
    fontFamily: {
      sans: ["Open Sans", ...fontFamily.sans],
      serif: ["feijoa", ...fontFamily.serif],
      display: ["feijoa", ...fontFamily.serif],
    },
    container: {
      center: true,
    },
    screens: {
      xs: "520px",
      sm: "768px",
      md: "1024px",
      lg: "1280px",
      xl: "1536px",
      "2xl": "1800px",
    },
    boxShadow: {
      none: "none",
      xs: "0px 1px 2px 0px rgba(16, 24, 40, 0.05)",
      sm: "0px 1px 3px 0px rgba(16, 24, 40, 0.10), 0px 1px 2px 0px rgba(16, 24, 40, 0.06)",
      md: "0px 4px 8px -2px rgba(1, 10, 20, 0.10), 0px 2px 4px -2px rgba(1, 10, 20, 0.06)",
      lg: "0px 12px 16px -4px rgba(1, 10, 20, 0.08), 0px 4px 6px -2px rgba(1, 10, 20, 0.03)",
      xl: "0px 20px 24px -4px rgba(1, 10, 20, 0.07), 0px 8px 8px -4px rgba(1, 10, 20, 0.03)",
    },
    fontWeight: {
      regular: "400",
      semibold: "550",
      bold: "660",
    },
    spacing: {
      0: "0px",
      2: "2px",
      4: "4px",
      8: "8px",
      12: "12px",
      16: "16px",
      20: "20px",
      24: "24px",
      32: "32px",
      36: "36px",
      40: "40px",
      48: "48px",
      64: "64px",
      76: "76px",
      80: "80px",
      96: "96px",
      120: "120px",
      160: "160px",
    },
    borderRadius: {
      none: "0",
      4: "4px",
      8: "8px",
      12: "12px",
      16: "16px",
      full: "9999px",
    },
    colors: {
      ...colors,
      text: {
        primary: colors.blue[900],
        secondary: colors.blue[900] + "D9",
        "contrast-secondary": "rgba(255, 255, 255, 0.88)",
        tertiary: colors.blue[900] + "BF",
        inverse: colors.white + "E0",
      },
      surface: {
        default: colors.green[100],
        brand: colors.blue[75],
        secondary: colors.white,
        tertiary: colors.green[200],
        tint: "#242929", // 10% opacity
        inverse: colors.blue[700],
        overlay: "#242929", // 70% opacity
      },
      divider: {
        subtle: colors.blue[700] + "26",
        strong: colors.blue[700] + "59",
        "contrast-subtle": colors.white + "26",
      },
      link: {
        default: colors.blue[900],
        hover: colors.cherry[200],
        active: colors.cherry[300],
        contrast: colors.blue[50],
        "contrast-hover": colors.blue[200],
        "contrast-active": colors.blue[100],
      },
      light: {
        teal: "#C5EEEA",
        purple: "#ECE3F6",
        cherry: "#F7D9E3",
        green: "#DAF2E9",
        grey: "#E9ECF1",
        blue: "#CFE5F4",
      },
    },
    extend: {
      typography: ({ theme }: PluginAPI) => ({
        DEFAULT: {
          css: {
            "--tw-prose-bullets": theme("colors.text.secondary"),
            "--tw-prose-counters": theme("colors.text.secondary"),
            "--tw-prose-th-borders": "#E9EBEF",
            "--tw-prose-td-borders": "#E9EBEF",
            a: {
              // Reset the default link styles
              "font-weight": theme("fontWeight.regular"),
              "text-decoration": "none",
            },
          },
        },
      }),
      rotate: {
        0: "0deg",
        90: "90deg",
        180: "180deg",
      },
    },
  },
  plugins: [
    function ({ addUtilities, addBase, theme }: PluginAPI) {
      const textSizes = {
        ".text-size-1": {
          fontSize: "15px",
        },
        ".text-size-2": {
          fontSize: "16px",
        },
        ".text-size-3": {
          fontSize: "18px",
        },
        ".text-size-4": {
          fontSize: "20px",
        },
        ".text-size-5": {
          fontSize: "22px",
          "@screen md": {
            fontSize: "23px",
          },
        },
        ".text-size-6": {
          fontSize: "26px",
          "@screen md": {
            fontSize: "28px",
          },
        },
        ".text-size-7": {
          fontSize: "30px",
          "@screen md": {
            fontSize: "32px",
          },
          "@screen lg": {
            fontSize: "34px",
          },
        },
        ".text-size-8": {
          fontSize: "34px",
          "@screen md": {
            fontSize: "44px",
          },
        },
      };
      addBase({ ...textSizes });

      const textStyles = {
        ".text-style-heading-title": {
          fontFamily: theme("fontFamily.serif") as CSSRuleObject,
          lineHeight: "1.35",
          fontWeight: "400",
          "@apply text-size-8": {},
        },
        ".text-style-heading-display": {
          fontFamily: theme("fontFamily.serif") as CSSRuleObject,
          lineHeight: "1.4",
          fontWeight: "400",
          "@apply text-size-7": {},
        },
        ".text-style-heading-lg": {
          fontFamily: theme("fontFamily.serif") as CSSRuleObject,
          lineHeight: "1.4",
          fontWeight: "400",
          "@apply text-size-6": {},
        },
        ".text-style-heading-md": {
          fontFamily: theme("fontFamily.sans") as CSSRuleObject,
          lineHeight: "1.45",
          fontWeight: "550",
          "@apply text-size-5": {},
        },
        ".text-style-heading-sm": {
          fontFamily: theme("fontFamily.sans") as CSSRuleObject,
          lineHeight: "1.5",
          fontWeight: "550",
          "@apply text-size-4": {},
        },
        ".text-style-heading-xs": {
          fontFamily: theme("fontFamily.sans") as CSSRuleObject,
          lineHeight: "1.48",
          fontWeight: "550",
          "@apply text-size-3": {},
        },
        ".text-style-body-lg": {
          fontFamily: theme("fontFamily.sans") as CSSRuleObject,
          lineHeight: "1.5",
          letterSpacing: "0.002em",
          fontWeight: "400",
          textWrap: "pretty",
          "@apply text-size-3": {},
        },
        ".text-style-body-md": {
          fontFamily: theme("fontFamily.sans") as CSSRuleObject,
          lineHeight: "1.5",
          letterSpacing: "0.003em",
          fontWeight: "400",
          textWrap: "pretty",
          "@apply text-size-2": {},
        },
        ".text-style-body-sm": {
          fontFamily: theme("fontFamily.sans") as CSSRuleObject,
          lineHeight: "1.55",
          letterSpacing: "0.004em",
          fontWeight: "400",
          textWrap: "pretty",
          "@apply text-size-1": {},
        },
      } satisfies CSSRuleObject;

      const divide = {
        ".divide-middot": {
          "& > *:not(:last-child)::after": {
            content: '"·"',
            marginLeft: "4px",
            marginRight: "4px",
          },
        },
      };

      addUtilities({ ...textStyles, ...divide });
    },
    typography,
    forms,
  ],
} satisfies Config;
