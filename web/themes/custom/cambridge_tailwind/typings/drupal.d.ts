export interface DrupalInstance {
  behaviors: {
    [key: string]: {
      attach: (context: HTMLElement) => void;
    };
  };
}

export type OnceFn = (
  id: string,
  selector: string,
  context: HTMLElement,
) => HTMLElement[];
