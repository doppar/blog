document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search-input");
  const searchResults = document.getElementById("search-results");
  let debounceTimer;

  if (!searchInput || !searchResults) return;

  // Function to highlight matching text
  function highlightText(text, query) {
    if (!query) return text;
    const regex = new RegExp(
      `(${query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`,
      "gi",
    );
    return text.replace(regex, '<span class="search-highlight">$1</span>');
  }

  searchInput.addEventListener("input", function (e) {
    const query = e.target.value.trim();

    clearTimeout(debounceTimer);

    if (query.length < 2) {
      searchResults.classList.add("hidden");
      return;
    }

    // Show loading state
    searchResults.innerHTML = `
      <div class="search-empty">
        <svg class="animate-spin" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <div>Searching...</div>
      </div>
    `;
    searchResults.classList.remove("hidden");

    debounceTimer = setTimeout(() => {
      fetch(`/api/search/posts?q=${encodeURIComponent(query)}`)
        .then((response) => response.json())
        .then((posts) => {
          if (posts.length === 0) {
            searchResults.innerHTML = `
              <div class="search-empty">
                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <div>No posts found</div>
              </div>
            `;
          } else {
            searchResults.innerHTML = posts
              .map(
                (post) => `
                  <a href="${post.url}" class="search-result-item">
                    <div class="title">${highlightText(post.title, query)}</div>
                    <div class="excerpt">${highlightText(post.excerpt, query)}</div>
                    <div class="meta">
                      ${post.category ? `<span class="category-badge">${post.category}</span>` : ""}
                      <span class="date">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        ${post.created_at}
                      </span>
                    </div>
                  </a>
                `,
              )
              .join("");
          }
        })
        .catch((error) => {
          console.error("Search error:", error);
          searchResults.innerHTML = `
            <div class="search-empty">
              <div>Error loading results</div>
            </div>
          `;
        });
    }, 300);
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
      searchResults.classList.add("hidden");
    }
  });

  // Close on Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      searchResults.classList.add("hidden");
    }
  });
});
