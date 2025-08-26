import React from 'react';

const AuthorForm = () => {
    return (
        <div className="container mt-4">
            <h2 className="mb-4">Add/Edit Author</h2>
            <form>
                <div className="mb-3">
                    <label htmlFor="name" className="form-label">Name</label>
                    <input type="text" className="form-control" id="name" placeholder="Enter author name" />
                </div>
                <button type="submit" className="btn btn-primary">Submit</button>
            </form>
        </div>
    );
};

export default AuthorForm;
