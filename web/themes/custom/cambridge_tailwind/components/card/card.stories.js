import Card from "./card.twig"

export default {
  title: "Components/Card",
  argTypes: {
    heading: {
      control: { type: "text" },
    },
    subheading: {
      control: { type: "text" },
    },
    description: {
      control: { type: "text" },
    },
    link: {
      control: { type: "text" },
    },
  },
  component: Card,
}

export const Default = {
  args: {
    heading: "Heading",
    subheading: "Subheading",
    description: "Description text",
    link: "Link",
  },
}
