import type { DrupalInstance, OnceFn } from "../../typings/drupal";
import handleOverlayPanels from "./src/handleOverlayPanels";
import setupMoreDropdownButton from "./src/setupMoreDropdownButton";
declare const Drupal: DrupalInstance;
declare const once: OnceFn;

((Drupal, once) => {
  Drupal.behaviors.mainMenu = {
    attach: (context: HTMLElement) => {
      once("mainMenu", "#menu-wrapper", context).forEach(
        (context: HTMLElement) => {
          // Handle the hiding and showing of overlay panels on clicking the menu items
          handleOverlayPanels(context);

          // Handle the hiding and showing of the "More" button and its dropdown
          // If required based on the width of the menu items and the viewport
          setupMoreDropdownButton(context);
        },
      );
    },
  };
})(Drupal, once);
