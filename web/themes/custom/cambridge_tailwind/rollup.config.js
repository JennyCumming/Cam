import typescript from "@rollup/plugin-typescript";
import terser from "@rollup/plugin-terser";

export default [
  {
    input: "components/menu/main-menu.ts",
    output: {
      file: "components/menu/main-menu.js",
      format: "cjs",
    },
    plugins: [
      typescript(),
      // We use terser to minify the output
      terser(),
    ],
  },
];
