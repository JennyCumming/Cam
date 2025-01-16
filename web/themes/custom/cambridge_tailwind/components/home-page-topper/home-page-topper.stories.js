import HomePageTopper from "./home-page-topper.twig"

export default {
  title: "Components/HomePageTopper",
  argTypes: {
    title: {
      control: { type: "text" },
    },
    title_prefix: {
      control: { type: "text" },
    },
  },
  component: HomePageTopper,
}

export const Default = {
  args: {
    title: "English",
    title_prefix: "Department of",
  },
}
