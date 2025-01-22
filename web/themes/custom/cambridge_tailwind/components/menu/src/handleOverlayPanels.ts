export default (context: HTMLElement) => {
  const overlay = context.querySelector("#menu-overlay") as HTMLElement;

  if (!overlay) {
    throw new Error("Menu overlay not found");
  }

  const moreButton =
    context.querySelector<HTMLButtonElement>("#nav-more-button");

  if (!moreButton) {
    throw new Error("More button not found");
  }

  // Event listeners
  const escButtonPress = (event: KeyboardEvent) => {
    if (event.key === "Escape") {
      closeDropdown();
    }
  };

  const overlayClick = () => {
    closeDropdown();
  };

  const menuItemFocusIn = () => {
    return closeDropdown();
  };

  const attachOpenListeners = () => {
    overlay.addEventListener("click", overlayClick);
    window.addEventListener("keydown", escButtonPress);

    const allMenuLinks = context.querySelectorAll("ul#menu-items > li > a");
    // Close the dropdown panel if you tab to the menu links outside of it
    allMenuLinks.forEach((link) => {
      link.addEventListener("focusin", menuItemFocusIn);
    });
    moreButton.addEventListener("focusin", menuItemFocusIn);
  };

  const removeOpenListeners = () => {
    window.removeEventListener("keydown", escButtonPress);
    overlay.removeEventListener("click", overlayClick);

    const allMenuLinks = context.querySelectorAll("ul#menu-items > li > a");
    // Close the dropdown panel if you tab to the menu links outside of it
    allMenuLinks.forEach((link) => {
      link.removeEventListener("focusin", menuItemFocusIn);
    });
    moreButton.removeEventListener("focusin", menuItemFocusIn);
  };

  // Helper functions
  const getPanel = (id: string) => {
    const panel = context.querySelector('[data-menu-panel="' + id + '"]');

    if (!panel) {
      throw new Error("Panel not found for panel " + id);
    }

    return panel;
  };

  // The more dropdown has duplicates of the buttons
  // So we need to handle the states across all of them, in case resizing shows these
  const getButtons = (id: string) => {
    const buttons = context.querySelectorAll(
      '[data-menu-control="' + id + '"]',
    );

    const output: { button: Element; svg: SVGElement }[] = [];

    buttons.forEach((button) => {
      const svg = button.querySelector("svg");

      if (!svg) {
        throw new Error("SVG not found for button " + id);
      }

      output.push({
        button,
        svg,
      });
    });

    return output;
  };

  const getButton = (id: string) => {
    const buttons = getButtons(id);
    if (!buttons.length) {
      throw new Error("Button not found for panel " + id);
    }

    return buttons[0];
  };

  // Triggered on click of a dropdown button
  const togglePanel = (panelId: string | null) => {
    if (!panelId) {
      throw new Error("Panel ID not found.");
    }

    const { button } = getButton(panelId);

    const isOpen = button.getAttribute("aria-expanded") === "true";
    // If it's open, we're closing itself, so want to hide the overlay
    return isOpen ? closeDropdown() : openPanel(panelId);
  };

  // Open a panel
  const openPanel = (panelId: string) => {
    const buttons = getButtons(panelId);
    const panel = getPanel(panelId);

    // Show the overlay, if not already shown
    if (overlay.classList.contains("hidden")) {
      overlay.classList.remove("hidden");
      // And attach the event listeners if we're just opening it now
      attachOpenListeners();
    }

    // Hide all other panels
    // (Should only ever be one but play it safe...)
    context
      .querySelectorAll(
        '[data-menu-control][aria-expanded="true"]:not([data-menu-control="' +
          panelId +
          '"])',
      )
      .forEach((panel) => {
        const panelId = panel.getAttribute("data-menu-control");
        if (panelId) {
          closePanel(panelId);
        } else {
          console.warn("Panel ID not found for panel " + panel);
        }
      });

    // Show the panel
    buttons.forEach(({ button, svg }) => {
      button.setAttribute("aria-expanded", "true");
      svg.classList.add("rotate-180");
    });
    panel.classList.remove("hidden");

    // Focus on the first focusable element in the panel
    const firstLink = panel.querySelector("a");
    if (!firstLink) {
      console.warn("First link not found for panel " + panelId);
    } else {
      firstLink.focus();
    }
  };

  // Close a panel
  const closePanel = (panelId: string) => {
    const buttons = getButtons(panelId);
    buttons.forEach(({ button, svg }) => {
      button.setAttribute("aria-expanded", "false");
      svg.classList.remove("rotate-180");
    });

    const panel = getPanel(panelId);
    panel.classList.add("hidden");
  };

  // Close all panels
  const closeDropdown = () => {
    // Hide the overley
    overlay.classList.add("hidden");

    // Hide all visible panels
    context
      .querySelectorAll('[data-menu-control][aria-expanded="true"]')
      .forEach((button) => {
        const panelId = button.getAttribute("data-menu-control");
        if (panelId) {
          closePanel(panelId);
        } else {
          console.warn("Panel ID not found for button " + button);
        }
      });

    removeOpenListeners();
  };

  // Enhance the dropdown links
  const dropdownLinks = context.querySelectorAll("a[data-menu-control]");
  dropdownLinks.forEach((link) => {
    const panelId = link.getAttribute("data-menu-control");

    if (!panelId) {
      throw new Error("Panel ID not found for link " + link);
    }

    link.setAttribute("aria-expanded", "false");
    link.setAttribute("aria-haspopup", "true");
    link.setAttribute("aria-controls", panelId);

    link.addEventListener("mousedown", (event) => {
      // Stop focus listener firing before click
      // Making the open and closed states fight each other
      event.preventDefault();
    });

    // Then stops the default behaviour of the href on the link from being followed
    link.addEventListener("click", (event) => {
      event.preventDefault();
      return togglePanel(panelId);
    });
  });
};
