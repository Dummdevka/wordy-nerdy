<div class="admin form">
    <!-- Add urls -->
    <div class="admin__block " id="add-url">
        <form action="/wordy/add-url" method="post" class="admin__form form">
            <input type="text" name="url" placeholder="New url..." class="admin__input">
            <select name="category" id="select-category" class="admin__select">
                <option value="1">Literature</option>
                <option value="2">Fashion</option>
                <option value="3">Nature</option>
            </select>
            <button type="submit" class="admin__btn btn-submit">Add</button>
        </form>
    </div>
    <div class="admin__block" id="add-book">
        <form action="/wordy/add-book" method="post" class="admin__form form">
            <input type="file" name="book" class="admin__file">
            <input type="text" name="author" id="book-author" class="admin__input">
            <button type="submit" class="admin__btn btn-submit">Add</button>
        </form>
    </div>
</div>