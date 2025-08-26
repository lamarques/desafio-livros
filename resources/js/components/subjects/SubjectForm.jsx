import React from 'react';

const SubjectForm = () => {
    return (
        <div className="container mt-4">
            <h2 className="mb-4">Add/Edit Subject</h2>
            <form>
                <div className="mb-3">
                    <label htmlFor="name" className="form-label">Name</label>
                    <input type="text" className="form-control" id="name" placeholder="Enter subject name" />
                </div>
                <button type="submit" className="btn btn-primary">Submit</button>
            </form>
        </div>
    );
};

export default SubjectForm;
