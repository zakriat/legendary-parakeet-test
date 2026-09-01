<div
    class="modal fade"
    id="gmcVerificationModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-lg modal-dialog-centered"
    >
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        GMC Registration Check
                    </h5>

                    <small class="text-muted">
                        Verify against the official GMC register
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>

            <div class="modal-body">
                <div
                    id="gmc-loading"
                    class="text-center py-5 d-none"
                >
                    <div
                        class="spinner-border text-primary"
                    ></div>
                </div>

                <div id="gmc-content">
                    <div class="card bg-body-tertiary mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">
                                        Doctor
                                    </small>

                                    <strong id="gmc-doctor-name">
                                        —
                                    </strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">
                                        GMC Number
                                    </small>

                                    <strong id="gmc-number">
                                        —
                                    </strong>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted d-block">
                                        Status
                                    </small>

                                    <span
                                        id="gmc-status"
                                        class="badge bg-secondary"
                                    >
                                        Not checked
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        id="gmc-invalid"
                        class="alert alert-warning d-none"
                    >
                        This doctor does not have a valid
                        seven-digit GMC number. Update the
                        doctor record first.
                    </div>

                    <div
                        id="gmc-valid-content"
                        class="d-none"
                    >
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <a
                                href="#"
                                id="gmc-official-link"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-primary"
                            >
                                <i class="ph ph-arrow-square-out me-1"></i>
                                Open Official GMC Record
                            </a>

                            <button
                                type="button"
                                id="gmc-begin-button"
                                class="btn btn-outline-primary"
                            >
                                Start New Check
                            </button>
                        </div>

                        <form id="gmc-confirm-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Name on GMC register
                                    </label>

                                    <input
                                        type="text"
                                        name="registered_name"
                                        id="gmc-registered-name"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Registration status
                                    </label>

                                    <input
                                        type="text"
                                        name="registration_status"
                                        id="gmc-registration-status"
                                        class="form-control"
                                        placeholder="Registered with a licence to practise"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Licence to practise
                                    </label>

                                    <select
                                        name="has_licence_to_practise"
                                        id="gmc-has-licence"
                                        class="form-select"
                                        required
                                    >
                                        <option value="">
                                            Select
                                        </option>

                                        <option value="1">
                                            Holds a licence
                                        </option>

                                        <option value="0">
                                            Does not hold a licence
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Doctor name matches
                                    </label>

                                    <select
                                        name="name_matches"
                                        id="gmc-name-matches"
                                        class="form-select"
                                        required
                                    >
                                        <option value="">
                                            Select
                                        </option>

                                        <option value="1">
                                            Yes, name matches
                                        </option>

                                        <option value="0">
                                            No, name mismatch
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        Notes
                                    </label>

                                    <textarea
                                        name="notes"
                                        id="gmc-notes"
                                        class="form-control"
                                        rows="2"
                                    ></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            name="official_record_checked"
                                            id="gmc-official-checked"
                                            value="1"
                                            class="form-check-input"
                                            required
                                        >

                                        <label
                                            for="gmc-official-checked"
                                            class="form-check-label"
                                        >
                                            I checked the current
                                            official GMC record.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-success mt-3"
                            >
                                Save Verification
                            </button>
                        </form>

                        <hr>

                        <form
                            id="gmc-certificate-form"
                            enctype="multipart/form-data"
                        >
                            <label class="form-label">
                                Supporting document
                            </label>

                            <div class="input-group">
                                <input
                                    type="file"
                                    name="certificate"
                                    class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                >

                                <button
                                    type="submit"
                                    class="btn btn-outline-secondary"
                                >
                                    Upload Securely
                                </button>
                            </div>

                            <small class="text-muted">
                                Supporting evidence only. The live GMC
                                record remains the current source.
                            </small>
                        </form>

                        <a
                            href="#"
                            id="gmc-certificate-download"
                            class="btn btn-link px-0 mt-2 d-none"
                        >
                            Download Saved Document
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>