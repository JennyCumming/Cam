export default (context: HTMLElement) => {
  const menuItems = context.querySelector("#menu-items");

  if (!menuItems) {
    throw new Error("Menu bar not found");
  }

  // We've hydrated the page with JS
  // So we can stop the overflowing menu items from wrapping, as we're going to add in the "More" dropdown
  // Doing this means if JS is disabled or fails, the menu items will crudely wrap, and all menu options will still be accessible
  menuItems.classList.remove("flex-wrap");

  const moreButton =
    context.querySelector<HTMLButtonElement>("#nav-more-button");
  const moreMenu = context.querySelector("#nav-more-menu");
  const moreContainer = context.querySelector("#nav-more-container");

  if (!moreButton || !moreMenu || !moreContainer) {
    throw new Error("More button / menu / container / wrapper not found");
  }

  const windowClick = (event: MouseEvent) => {
    // If click outside the dropdown, close it
    if (!moreContainer.contains(event.target as Node)) {
      closeMoreMenu();
    }
  };

  const menuItemFocusIn = () => {
    closeMoreMenu();
  };

  const escButtonPress = (event: KeyboardEvent) => {
    if (event.key === "Escape") {
      closeMoreMenu();
    }
  };

  const openMoreMenu = () => {
    moreButton.setAttribute("aria-expanded", "true");
    moreMenu.setAttribute("aria-hidden", "false");
    moreMenu.classList.remove("hidden");

    // Focus on the first item in the dropdown
    const firstLink = moreMenu.querySelector<HTMLAnchorElement>(
      "li:not(.hidden) > a",
    );
    if (firstLink) firstLink.focus();

    // ESC key should close the dropdown
    window.addEventListener("keydown", escButtonPress);

    // Clicking outside of the dropdown should close it
    window.addEventListener("click", windowClick);

    // On focus of a main nav menu item, close the dropdown
    const allMenuLinks = menuItems.querySelectorAll("& > li > a");
    // Close the dropdown panel if you tab to the menu links outside of it
    allMenuLinks.forEach((link) => {
      link.addEventListener("focusin", menuItemFocusIn);
    });

    moreButton.addEventListener("focusin", menuItemFocusIn);
  };

  const closeMoreMenu = () => {
    // Check if focus is inside the menu
    if (moreMenu.contains(document.activeElement)) {
      // If so, focus on the "More" button
      moreButton.focus();
    }

    moreButton.setAttribute("aria-expanded", "false");
    moreMenu.setAttribute("aria-hidden", "true");
    moreMenu.classList.add("hidden");

    // Remove listeners to close the dropdown
    window.removeEventListener("keydown", escButtonPress);
    window.removeEventListener("click", windowClick);
    const allMenuLinks = menuItems.querySelectorAll("& > li > a");
    // Close the dropdown panel if you tab to the menu links outside of it
    allMenuLinks.forEach((link) => {
      link.removeEventListener("focusin", menuItemFocusIn);
    });

    moreButton.removeEventListener("focusin", menuItemFocusIn);
  };

  const showMoreButton = () => {
    moreContainer.setAttribute("aria-hidden", "false");
    moreContainer.classList.remove("hidden");
  };

  const hideMoreButton = () => {
    // Check if focus is on the "More" button
    if (moreButton === document.activeElement) {
      // If so, focus on the last item in the main nav
      const lastLink =
        menuItems.querySelector<HTMLAnchorElement>("a:last-of-type");
      if (lastLink) lastLink.focus();
    }

    closeMoreMenu();
    moreContainer.setAttribute("aria-hidden", "true");
    moreContainer.classList.add("hidden");
  };

  const isMenuItemVisibleInViewport = (element: Element) => {
    const rect = element.getBoundingClientRect();
    const itemsRect = menuItems.getBoundingClientRect();
    return rect.right <= itemsRect.right;
  };

  const moveMenuItemToMore = (item: Element) => {
    const link = item.querySelector("& > a");
    if (!link) throw new Error("Link not found for item " + item.id);
    link.classList.add("hidden");
    link.setAttribute("aria-hidden", "true");
    const moreMenuItem = moreMenu.querySelector("#more-" + item.id);
    if (!moreMenuItem)
      throw new Error("More menu item not found for ID " + item.id);
    moreMenuItem.classList.remove("hidden");
    moreMenuItem.setAttribute("aria-hidden", "false");
  };

  const moveMenuItemToMain = (item: Element) => {
    const link = item.querySelector("& > a");
    if (!link) throw new Error("Link not found for item " + item.id);
    link.classList.remove("hidden");
    link.setAttribute("aria-hidden", "false");
    const moreMenuItem = moreMenu.querySelector("#more-" + item.id);
    if (!moreMenuItem)
      throw new Error("More menu item not found for ID " + item.id);
    moreMenuItem.classList.add("hidden");
    moreMenuItem.setAttribute("aria-hidden", "true");
  };

  // On resize, recalculate if the "More" dropdown is required
  // and what should be shown in it / thus hidden in the main nav bar
  const resizeObserver = new ResizeObserver(() => {
    // We have resized, so hide the button.
    hideMoreButton();

    const hiddenItems: Element[] = [];
    const visibleItems: Element[] = [];
    const items = menuItems.querySelectorAll("& > li:not(#nav-more-container)");

    // Make them all visible to do the calculation.
    items.forEach((item) => {
      moveMenuItemToMain(item);
    });

    items.forEach((item) => {
      if (!isMenuItemVisibleInViewport(item)) {
        hiddenItems.push(item);
      } else {
        visibleItems.push(item);
      }
    });

    if (hiddenItems.length) {
      hiddenItems.forEach((item) => {
        moveMenuItemToMore(item);
      });

      showMoreButton();

      // Check if the more button is fully visible
      while (!isMenuItemVisibleInViewport(moreButton) && visibleItems.length) {
        const lastLink = visibleItems.pop();
        if (!lastLink) return;
        // Move the last item from the main nav to the more dropdown
        moveMenuItemToMore(lastLink);
      }
    }
  });

  resizeObserver.observe(menuItems);

  // On click of the "more" dropdown, toggle the display of the dropdown
  moreButton.addEventListener("click", () => {
    if (moreButton.getAttribute("aria-expanded") === "true") {
      closeMoreMenu();
    } else {
      openMoreMenu();
    }
  });

  // On click of a more dropdown item that shows the overlay, close the dropdown
  moreMenu.querySelectorAll("a[data-menu-control]").forEach((link) => {
    link.addEventListener("click", () => {
      closeMoreMenu();
    });
  });
};
